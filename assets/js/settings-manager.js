class SettingsManager {
  constructor() {
    this.initColorPickers()
    this.initPreviewMode()
  }

  initColorPickers() {
    const colorInputs = document.querySelectorAll('input[type="color"]')
    colorInputs.forEach((input) => {
      input.addEventListener("change", (e) => {
        this.updateColorPreview(e.target.name, e.target.value)
      })
    })
  }

  updateColorPreview(colorType, colorValue) {
    const root = document.documentElement

    switch (colorType) {
      case "primary_color":
        root.style.setProperty("--primary-color", colorValue)
        break
      case "secondary_color":
        root.style.setProperty("--secondary-color", colorValue)
        break
      case "accent_color":
        root.style.setProperty("--accent-color", colorValue)
        break
    }

    // Update gradient backgrounds
    const primaryColor = getComputedStyle(root).getPropertyValue("--primary-color")
    const secondaryColor = getComputedStyle(root).getPropertyValue("--secondary-color")

    const gradientElements = document.querySelectorAll(".btn-primary, .sidebar, .card-header.bg-primary")
    gradientElements.forEach((element) => {
      if (element.classList.contains("sidebar")) {
        element.style.background = `linear-gradient(180deg, ${primaryColor} 0%, ${secondaryColor} 100%)`
      } else {
        element.style.background = `linear-gradient(135deg, ${primaryColor} 0%, ${secondaryColor} 100%)`
      }
    })
  }

  initPreviewMode() {
    const previewBtn = document.getElementById("previewMode")
    if (previewBtn) {
      previewBtn.addEventListener("click", () => {
        this.togglePreviewMode()
      })
    }
  }

  togglePreviewMode() {
    const body = document.body
    body.classList.toggle("preview-mode")

    if (body.classList.contains("preview-mode")) {
      this.showPreviewOverlay()
    } else {
      this.hidePreviewOverlay()
    }
  }

  showPreviewOverlay() {
    const overlay = document.createElement("div")
    overlay.id = "preview-overlay"
    overlay.className =
      "position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center"
    overlay.style.zIndex = "9999"

    overlay.innerHTML = `
            <div class="bg-white p-4 rounded shadow">
                <h5>Preview Mode Active</h5>
                <p>You are viewing the site with current settings.</p>
                <button class="btn btn-primary" onclick="this.closest('#preview-overlay').remove(); document.body.classList.remove('preview-mode')">
                    Exit Preview
                </button>
            </div>
        `

    document.body.appendChild(overlay)
  }

  async saveSettings(formData) {
    try {
      const response = await fetch("/api/settings.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          action: "update_settings",
          settings: formData,
        }),
      })

      const result = await response.json()

      if (result.success) {
        this.showNotification("Settings saved successfully!", "success")
        // Reload dynamic styles
        this.reloadDynamicStyles()
      } else {
        this.showNotification("Failed to save settings: " + result.message, "error")
      }
    } catch (error) {
      this.showNotification("Error saving settings: " + error.message, "error")
    }
  }

  reloadDynamicStyles() {
    const dynamicStylesheet = document.querySelector('link[href*="dynamic-styles.php"]')
    if (dynamicStylesheet) {
      const newHref = dynamicStylesheet.href.split("?")[0] + "?v=" + Date.now()
      dynamicStylesheet.href = newHref
    }
  }

  showNotification(message, type = "info") {
    const notification = document.createElement("div")
    notification.className = `alert alert-${type === "error" ? "danger" : type} alert-dismissible fade show position-fixed`
    notification.style.cssText = "top: 20px; right: 20px; z-index: 10000; min-width: 300px;"

    notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `

    document.body.appendChild(notification)

    setTimeout(() => {
      if (notification.parentNode) {
        notification.remove()
      }
    }, 5000)
  }
}

// Initialize settings manager when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  window.settingsManager = new SettingsManager()
})
