// Registration form functionality
document.addEventListener("DOMContentLoaded", () => {
  initializeAccountTypeSelection()
  initializePasswordToggle()
  initializeFormValidation()
})

// Initialize account type selection
function initializeAccountTypeSelection() {
  const accountTypeCards = document.querySelectorAll(".account-type-card")
  const providerFields = document.getElementById("provider-fields")
  const userTypeInputs = document.querySelectorAll('input[name="user_type"]')

  accountTypeCards.forEach((card) => {
    card.addEventListener("click", function () {
      const type = this.dataset.type
      const radio = this.querySelector('input[type="radio"]')

      // Update visual selection
      accountTypeCards.forEach((c) => c.classList.remove("border-primary", "bg-primary", "bg-opacity-10"))
      this.classList.add("border-primary", "bg-primary", "bg-opacity-10")

      // Update radio selection
      radio.checked = true

      // Show/hide provider fields
      if (type === "provider") {
        providerFields.style.display = "block"
        makeProviderFieldsRequired(true)
      } else {
        providerFields.style.display = "none"
        makeProviderFieldsRequired(false)
      }
    })
  })

  // Initialize with customer selected
  document.querySelector('.account-type-card[data-type="customer"]').click()
}

// Make provider fields required/optional
function makeProviderFieldsRequired(required) {
  const providerInputs = document.querySelectorAll(
    "#provider-fields input, #provider-fields select, #provider-fields textarea",
  )

  providerInputs.forEach((input) => {
    if (required) {
      input.setAttribute("required", "required")
    } else {
      input.removeAttribute("required")
      input.value = ""
    }
  })
}

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
  const form = document.getElementById("registrationForm")
  const passwordInput = document.getElementById("password")
  const confirmPasswordInput = document.getElementById("confirm_password")

  if (form) {
    // Real-time password confirmation validation
    confirmPasswordInput.addEventListener("input", function () {
      if (this.value !== passwordInput.value) {
        this.setCustomValidity("Passwords do not match")
        this.classList.add("is-invalid")
      } else {
        this.setCustomValidity("")
        this.classList.remove("is-invalid")
        this.classList.add("is-valid")
      }
    })

    // Password strength indicator
    passwordInput.addEventListener("input", function () {
      const strength = calculatePasswordStrength(this.value)
      updatePasswordStrengthIndicator(strength)
    })

    // Form submission
    form.addEventListener("submit", function (e) {
      if (!this.checkValidity()) {
        e.preventDefault()
        e.stopPropagation()
      }

      this.classList.add("was-validated")
    })
  }
}

// Calculate password strength
function calculatePasswordStrength(password) {
  let strength = 0
  const checks = {
    length: password.length >= 8,
    lowercase: /[a-z]/.test(password),
    uppercase: /[A-Z]/.test(password),
    numbers: /\d/.test(password),
    special: /[!@#$%^&*(),.?":{}|<>]/.test(password),
  }

  Object.values(checks).forEach((check) => {
    if (check) strength++
  })

  return {
    score: strength,
    checks: checks,
  }
}

// Update password strength indicator
function updatePasswordStrengthIndicator(strength) {
  let strengthText = ""
  let strengthClass = ""

  switch (strength.score) {
    case 0:
    case 1:
      strengthText = "Very Weak"
      strengthClass = "text-danger"
      break
    case 2:
      strengthText = "Weak"
      strengthClass = "text-warning"
      break
    case 3:
      strengthText = "Fair"
      strengthClass = "text-info"
      break
    case 4:
      strengthText = "Good"
      strengthClass = "text-primary"
      break
    case 5:
      strengthText = "Strong"
      strengthClass = "text-success"
      break
  }

  // Update or create strength indicator
  let indicator = document.getElementById("password-strength")
  if (!indicator) {
    indicator = document.createElement("div")
    indicator.id = "password-strength"
    indicator.className = "form-text"
    document.getElementById("password").parentNode.appendChild(indicator)
  }

  indicator.innerHTML = `Password strength: <span class="${strengthClass}">${strengthText}</span>`
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
