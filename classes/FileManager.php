<?php
/**
 * File Upload and Management Class
 * Handles file uploads, image processing, and cloud storage integration
 */

class FileManager {
    private $conn;
    private $upload_path;
    private $allowed_types;
    private $max_file_size;
    
    public function __construct($db) {
        $this->conn = $db;
        $this->upload_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
        $this->allowed_types = [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'document' => ['pdf', 'doc', 'docx', 'txt'],
            'video' => ['mp4', 'avi', 'mov', 'wmv']
        ];
        $this->max_file_size = 10 * 1024 * 1024; // 10MB
        
        // Create upload directories if they don't exist
        $this->createDirectories();
    }
    
    /**
     * Upload a file
     */
    public function uploadFile($file, $type = 'image', $user_id = null) {
        try {
            // Validate file
            $validation = $this->validateFile($file, $type);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }
            
            // Generate unique filename
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = $this->generateUniqueFilename($file_extension);
            
            // Determine upload directory
            $upload_dir = $this->upload_path . $type . 's/';
            $file_path = $upload_dir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                // Process image if it's an image file
                if ($type === 'image') {
                    $this->processImage($file_path);
                }
                
                // Save file record to database
                $file_record = $this->saveFileRecord([
                    'original_name' => $file['name'],
                    'filename' => $filename,
                    'file_path' => $file_path,
                    'file_type' => $type,
                    'file_size' => $file['size'],
                    'mime_type' => $file['type'],
                    'user_id' => $user_id
                ]);
                
                if ($file_record) {
                    return [
                        'success' => true,
                        'file_id' => $file_record['id'],
                        'filename' => $filename,
                        'url' => $this->getFileUrl($filename, $type),
                        'file_path' => $file_path
                    ];
                }
            }
            
            return ['success' => false, 'message' => 'Failed to upload file'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Upload error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Upload multiple files
     */
    public function uploadMultipleFiles($files, $type = 'image', $user_id = null) {
        $results = [];
        
        // Handle both single and multiple file uploads
        if (isset($files['name']) && is_array($files['name'])) {
            // Multiple files
            for ($i = 0; $i < count($files['name']); $i++) {
                $file = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
                
                $results[] = $this->uploadFile($file, $type, $user_id);
            }
        } else {
            // Single file
            $results[] = $this->uploadFile($files, $type, $user_id);
        }
        
        return $results;
    }
    
    /**
     * Validate uploaded file
     */
    private function validateFile($file, $type) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'message' => 'File upload error: ' . $file['error']];
        }
        
        // Check file size
        if ($file['size'] > $this->max_file_size) {
            return ['valid' => false, 'message' => 'File size exceeds maximum allowed size'];
        }
        
        // Check file type
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, $this->allowed_types[$type] ?? [])) {
            return ['valid' => false, 'message' => 'File type not allowed'];
        }
        
        // Additional security checks
        if ($type === 'image') {
            $image_info = getimagesize($file['tmp_name']);
            if (!$image_info) {
                return ['valid' => false, 'message' => 'Invalid image file'];
            }
        }
        
        return ['valid' => true];
    }
    
    /**
     * Process uploaded image (resize, optimize)
     */
    private function processImage($file_path) {
        try {
            $image_info = getimagesize($file_path);
            $mime_type = $image_info['mime'];
            
            // Create image resource based on type
            switch ($mime_type) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($file_path);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($file_path);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($file_path);
                    break;
                default:
                    return false;
            }
            
            if (!$image) return false;
            
            $original_width = imagesx($image);
            $original_height = imagesy($image);
            
            // Create thumbnails
            $this->createThumbnail($image, $file_path, 150, 150, '_thumb');
            $this->createThumbnail($image, $file_path, 400, 400, '_medium');
            
            // Optimize original image if it's too large
            if ($original_width > 1200 || $original_height > 1200) {
                $this->resizeImage($image, $file_path, 1200, 1200);
            }
            
            imagedestroy($image);
            
        } catch (Exception $e) {
            error_log("Image processing error: " . $e->getMessage());
        }
    }
    
    /**
     * Create image thumbnail
     */
    private function createThumbnail($source_image, $original_path, $max_width, $max_height, $suffix) {
        $original_width = imagesx($source_image);
        $original_height = imagesy($source_image);
        
        // Calculate new dimensions
        $ratio = min($max_width / $original_width, $max_height / $original_height);
        $new_width = intval($original_width * $ratio);
        $new_height = intval($original_height * $ratio);
        
        // Create new image
        $thumbnail = imagecreatetruecolor($new_width, $new_height);
        
        // Preserve transparency for PNG and GIF
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        
        // Resize image
        imagecopyresampled($thumbnail, $source_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
        
        // Save thumbnail
        $path_info = pathinfo($original_path);
        $thumbnail_path = $path_info['dirname'] . '/' . $path_info['filename'] . $suffix . '.' . $path_info['extension'];
        
        switch (strtolower($path_info['extension'])) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($thumbnail, $thumbnail_path, 85);
                break;
            case 'png':
                imagepng($thumbnail, $thumbnail_path, 8);
                break;
            case 'gif':
                imagegif($thumbnail, $thumbnail_path);
                break;
        }
        
        imagedestroy($thumbnail);
    }
    
    /**
     * Resize image
     */
    private function resizeImage($source_image, $original_path, $max_width, $max_height) {
        $original_width = imagesx($source_image);
        $original_height = imagesy($source_image);
        
        // Calculate new dimensions
        $ratio = min($max_width / $original_width, $max_height / $original_height);
        $new_width = intval($original_width * $ratio);
        $new_height = intval($original_height * $ratio);
        
        // Create new image
        $resized = imagecreatetruecolor($new_width, $new_height);
        
        // Preserve transparency
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        
        // Resize image
        imagecopyresampled($resized, $source_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
        
        // Save resized image
        $path_info = pathinfo($original_path);
        switch (strtolower($path_info['extension'])) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($resized, $original_path, 85);
                break;
            case 'png':
                imagepng($resized, $original_path, 8);
                break;
            case 'gif':
                imagegif($resized, $original_path);
                break;
        }
        
        imagedestroy($resized);
    }
    
    /**
     * Save file record to database
     */
    private function saveFileRecord($data) {
        $query = "INSERT INTO files 
                  SET original_name = :original_name,
                      filename = :filename,
                      file_path = :file_path,
                      file_type = :file_type,
                      file_size = :file_size,
                      mime_type = :mime_type,
                      user_id = :user_id,
                      created_at = NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        if ($stmt->execute()) {
            $data['id'] = $this->conn->lastInsertId();
            return $data;
        }
        
        return false;
    }
    
    /**
     * Generate unique filename
     */
    private function generateUniqueFilename($extension) {
        return uniqid() . '_' . time() . '.' . $extension;
    }
    
    /**
     * Get file URL
     */
    public function getFileUrl($filename, $type = 'image') {
        $base_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
        return $base_url . '/uploads/' . $type . 's/' . $filename;
    }
    
    /**
     * Delete file
     */
    public function deleteFile($file_id, $user_id = null) {
        // Get file record
        $query = "SELECT * FROM files WHERE id = :file_id";
        if ($user_id) {
            $query .= " AND user_id = :user_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':file_id', $file_id);
        if ($user_id) {
            $stmt->bindParam(':user_id', $user_id);
        }
        $stmt->execute();
        
        $file = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($file) {
            // Delete physical file
            if (file_exists($file['file_path'])) {
                unlink($file['file_path']);
                
                // Delete thumbnails if it's an image
                if ($file['file_type'] === 'image') {
                    $path_info = pathinfo($file['file_path']);
                    $thumb_path = $path_info['dirname'] . '/' . $path_info['filename'] . '_thumb.' . $path_info['extension'];
                    $medium_path = $path_info['dirname'] . '/' . $path_info['filename'] . '_medium.' . $path_info['extension'];
                    
                    if (file_exists($thumb_path)) unlink($thumb_path);
                    if (file_exists($medium_path)) unlink($medium_path);
                }
            }
            
            // Delete database record
            $delete_query = "DELETE FROM files WHERE id = :file_id";
            $delete_stmt = $this->conn->prepare($delete_query);
            $delete_stmt->bindParam(':file_id', $file_id);
            
            return $delete_stmt->execute();
        }
        
        return false;
    }
    
    /**
     * Get user files
     */
    public function getUserFiles($user_id, $type = null, $limit = 50) {
        $query = "SELECT * FROM files WHERE user_id = :user_id";
        
        if ($type) {
            $query .= " AND file_type = :type";
        }
        
        $query .= " ORDER BY created_at DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        if ($type) {
            $stmt->bindParam(':type', $type);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Create necessary directories
     */
    private function createDirectories() {
        $directories = [
            $this->upload_path . 'images/',
            $this->upload_path . 'documents/',
            $this->upload_path . 'videos/'
        ];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
    
    /**
     * Clean up old files (for maintenance)
     */
    public function cleanupOldFiles($days = 30) {
        $query = "SELECT * FROM files 
                  WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)
                  AND user_id IS NULL"; // Only cleanup files not associated with users
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $deleted_count = 0;
        
        foreach ($files as $file) {
            if ($this->deleteFile($file['id'])) {
                $deleted_count++;
            }
        }
        
        return $deleted_count;
    }
}
?>
