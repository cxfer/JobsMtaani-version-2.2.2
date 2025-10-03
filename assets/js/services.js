// Services page functionality
document.addEventListener("DOMContentLoaded", () => {
  initializeFilters()
  initializeFavorites()
})

// Initialize filter functionality
function initializeFilters() {
  const filterForm = document.querySelector('form[method="GET"]')
  const inputs = filterForm.querySelectorAll("input, select")

  // Auto-submit on filter change (with debounce for text inputs)
  inputs.forEach((input) => {
    if (input.type === "text") {
      let timeout
      input.addEventListener("input", function () {
        clearTimeout(timeout)
        timeout = setTimeout(() => {
          if (this.value.length >= 3 || this.value.length === 0) {
            filterForm.submit()
          }
        }, 500)
      })
    } else {
      input.addEventListener("change", () => {
        filterForm.submit()
      })
    }
  })
}

// Initialize favorites functionality
function initializeFavorites() {
  const favoriteButtons = document.querySelectorAll(".favorite-btn")

  favoriteButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault()
      toggleFavorite(this)
    })
  })
}

// Toggle favorite status
async function toggleFavorite(button) {
  const serviceId = button.dataset.serviceId
  const isFavorite = button.classList.contains("favorited")

  try {
    const response = await fetch("api/favorites.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        action: isFavorite ? "remove" : "add",
        service_id: serviceId,
      }),
    })

    const data = await response.json()

    if (data.success) {
      button.classList.toggle("favorited")
      const icon = button.querySelector("i")
      icon.classList.toggle("fas")
      icon.classList.toggle("far")

      showAlert(data.message, "success")
    } else {
      showAlert(data.message || "Error updating favorite", "error")
    }
  } catch (error) {
    console.error("Error toggling favorite:", error)
    showAlert("Error updating favorite", "error")
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
