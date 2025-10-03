// Login form functionality
document.addEventListener("DOMContentLoaded", () => {
  initializePasswordToggle()
  initializeFormValidation()
  initializeDemoAccounts()
})

// Initialize password toggle
function initializePasswordToggle() {
  const toggleButton = document.getElementById("togglePassword")
  const passwordInput = document.getElementById("password")

  if (toggleButton && passwordInput) {
    toggleButton.addEventListener("click", function () {
      const type = passwordInput.getAttribute("type") === "password" ? "text" : "password"
      passwordInput.setAttribute("type", type)

      const icon = this.querySelector("i")
      icon.classList.toggle("fa-eye")
      icon.classList.toggle("fa-eye-slash")
    })
  }
}

// Initialize form validation
function initializeFormValidation() {
  const form = document.getElementById("loginForm")

  if (form) {
    form.addEventListener("submit", function (e) {
      if (!this.checkValidity()) {
        e.preventDefault()
        e.stopPropagation()
      }

      this.classList.add("was-validated")
    })
  }
}

// Initialize demo accounts
function initializeDemoAccounts() {
  // Demo account functionality is handled by the fillDemo function
}

// Fill demo account credentials
function fillDemo(email, password) {
  document.getElementById("email").value = email
  document.getElementById("password").value = password

  // Add visual feedback
  showAlert("Demo credentials filled. Click Sign In to continue.", "info")
}

// Show alert message
function showAlert(message, type = "info") {
  const alertDiv = document.createElement("div")
  alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`
  alertDiv.style.cssText = "top: 20px; right: 20px; z-index: 9999; min-width: 300px;"
  alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `

  document.body.appendChild(alertDiv)

  setTimeout(() => {
    if (alertDiv.parentNode) {
      alertDiv.remove()
    }
  }, 5000)
}
