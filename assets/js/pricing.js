// Pricing page functionality
document.addEventListener("DOMContentLoaded", () => {
  initializeBillingToggle()
  initializeSubscriptionButtons()
  initializePaymentMethods()
})

// Import Bootstrap Modal
const bootstrap = window.bootstrap

// Initialize billing toggle (monthly/yearly)
function initializeBillingToggle() {
  const billingToggle = document.getElementById("billingToggle")
  const monthlyPrices = document.querySelectorAll(".monthly-price")
  const yearlyPrices = document.querySelectorAll(".yearly-price")
  const monthlyBilling = document.querySelectorAll(".monthly-billing")
  const yearlyBilling = document.querySelectorAll(".yearly-billing")

  if (billingToggle) {
    billingToggle.addEventListener("change", function () {
      const isYearly = this.checked

      monthlyPrices.forEach((el) => {
        el.classList.toggle("d-none", isYearly)
      })

      yearlyPrices.forEach((el) => {
        el.classList.toggle("d-none", !isYearly)
      })

      monthlyBilling.forEach((el) => {
        el.classList.toggle("d-none", isYearly)
      })

      yearlyBilling.forEach((el) => {
        el.classList.toggle("d-none", !isYearly)
      })
    })
  }
}

// Initialize subscription buttons
function initializeSubscriptionButtons() {
  const subscribeButtons = document.querySelectorAll(".subscribe-btn")
  const modal = new bootstrap.Modal(document.getElementById("subscriptionModal"))

  subscribeButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const planId = this.dataset.planId
      const planName = this.dataset.planName
      const planPrice = this.dataset.planPrice

      // Check if user is logged in
      if (!isUserLoggedIn()) {
        window.location.href = "login.php?redirect=" + encodeURIComponent(window.location.href)
        return
      }

      // Populate modal
      document.getElementById("modalPlanName").textContent = planName
      document.getElementById("modalPlanPrice").textContent = "KSh " + Number(planPrice).toLocaleString()
      document.getElementById("planId").value = planId

      modal.show()
    })
  })
}

// Initialize payment method selection
function initializePaymentMethods() {
  const paymentMethods = document.querySelectorAll('input[name="payment_method"]')
  const mpesaFields = document.getElementById("mpesaFields")

  paymentMethods.forEach((method) => {
    method.addEventListener("change", function () {
      if (this.value === "mpesa") {
        mpesaFields.style.display = "block"
      } else {
        mpesaFields.style.display = "none"
      }
    })
  })

  // Initialize subscription form
  const subscriptionForm = document.getElementById("subscriptionForm")
  if (subscriptionForm) {
    subscriptionForm.addEventListener("submit", handleSubscription)
  }
}

// Handle subscription form submission
async function handleSubscription(e) {
  e.preventDefault()

  const formData = new FormData(e.target)
  const data = Object.fromEntries(formData.entries())

  try {
    showLoading(true)

    const response = await fetch("api/subscriptions.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        action: "subscribe",
        ...data,
      }),
    })

    const result = await response.json()

    if (result.success) {
      if (data.payment_method === "mpesa") {
        showAlert("M-Pesa payment request sent to your phone. Please complete the payment.", "info")
        // Poll for payment status
        pollPaymentStatus(result.payment_id)
      } else {
        showAlert("Subscription successful!", "success")
        setTimeout(() => {
          window.location.reload()
        }, 2000)
      }
    } else {
      showAlert(result.message || "Subscription failed. Please try again.", "error")
    }
  } catch (error) {
    console.error("Subscription error:", error)
    showAlert("An error occurred. Please try again.", "error")
  } finally {
    showLoading(false)
  }
}

// Poll payment status for M-Pesa
async function pollPaymentStatus(paymentId, attempts = 0) {
  if (attempts >= 30) {
    // Stop polling after 5 minutes (30 attempts * 10 seconds)
    showAlert("Payment verification timeout. Please contact support if payment was deducted.", "warning")
    return
  }

  try {
    const response = await fetch(`api/payments.php?action=status&payment_id=${paymentId}`)
    const result = await response.json()

    if (result.success) {
      if (result.payment.status === "completed") {
        showAlert("Payment successful! Your subscription is now active.", "success")
        setTimeout(() => {
          window.location.reload()
        }, 2000)
        return
      } else if (result.payment.status === "failed") {
        showAlert("Payment failed. Please try again.", "error")
        return
      }
    }

    // Continue polling
    setTimeout(() => {
      pollPaymentStatus(paymentId, attempts + 1)
    }, 10000) // Poll every 10 seconds
  } catch (error) {
    console.error("Payment status check error:", error)
    setTimeout(() => {
      pollPaymentStatus(paymentId, attempts + 1)
    }, 10000)
  }
}

// Check if user is logged in
function isUserLoggedIn() {
  // This would typically check session or JWT token
  // For now, we'll check if there's a user menu or login link
  return document.querySelector(".user-menu") !== null
}

// Show loading state
function showLoading(show) {
  const submitButton = document.querySelector('#subscriptionForm button[type="submit"]')
  if (submitButton) {
    if (show) {
      submitButton.disabled = true
      submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...'
    } else {
      submitButton.disabled = false
      submitButton.innerHTML = '<i class="fas fa-credit-card me-2"></i>Subscribe Now'
    }
  }
}

// Show alert message
function showAlert(message, type = "info") {
  const alertDiv = document.createElement("div")
  alertDiv.className = `alert alert-${type === "error" ? "danger" : type} alert-dismissible fade show position-fixed`
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
