-- Add ID number and document fields to user_profiles table
ALTER TABLE user_profiles 
ADD COLUMN id_number VARCHAR(20) NULL AFTER phone_verified_at,
ADD COLUMN id_document_path VARCHAR(255) NULL AFTER id_number;