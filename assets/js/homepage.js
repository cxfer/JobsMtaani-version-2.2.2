// Homepage functionality
document.addEventListener("DOMContentLoaded", () => {
  loadPopularServices()
  initializeSearchForm()
})

// Load popular services
async function loadPopularServices() {
  try {
    const response = await fetch("api/services.php?action=popular&limit=6")
    const data = await response.json()

    if (data.success) {
      displayPopularServices(data.services)
    }
  } catch (error) {
    console.error("Error loading popular services:", error)
  }
}

// Display popular services
function displayPopularServices(services) {
  const container = document.getElementById("popular-services")

  if (services.length === 0) {
    container.innerHTML =
      '<div class="col-12 text-center"><p class="text-muted">No services available at the moment.</p></div>'
    return
  }

  container.innerHTML = services
    .map(
      (service) => `
        <div class="col-lg-4 col-md-6">
            <div class="card service-card h-100 border-0 shadow-sm">
                <div class="position-relative">
                    <img src="${service.image || "/abstract-service.png"}" 
                         class="card-img-top" alt="${service.title}" style="height: 200px; object-fit: cover;">
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-primary">${service.category}</span>
                    </div>
                </div>
                
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold">${service.title}</h5>
                    <p class="card-text text-muted flex-grow-1">${service.description.substring(0, 100)}...</p>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <img src="${service.provider_avatar || "/diverse-group-profile.png"}" 
                                 class="rounded-circle" width="40" height="40" alt="Provider">
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">${service.provider_name}</h6>
                            <div class="d-flex align-items-center">
                                <div class="text-warning me-1">
                                    ${generateStars(service.rating)}
                                </div>
                                <small class="text-muted">(${service.review_count})</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="h5 text-primary fw-bold">KSh ${Number(service.price).toLocaleString()}</span>
                            <small class="text-muted">/ ${service.price_unit}</small>
                        </div>
                        <a href="book-service.php?id=${service.id}" class="btn btn-primary">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `,
    )
    .join("")
}

// Generate star rating HTML
function generateStars(rating) {
  let stars = ""
  for (let i = 1; i <= 5; i++) {
    stars += `<i class="fas fa-star${i <= rating ? "" : "-o"}"></i>`
  }
  return stars
}

// Initialize search form
function initializeSearchForm() {
  const searchForm = document.querySelector('form[action="services.php"]')
  if (searchForm) {
    searchForm.addEventListener("submit", function (e) {
      const searchInput = this.querySelector('input[name="search"]')
      if (searchInput.value.trim() === "") {
        e.preventDefault()
        searchInput.focus()
        showAlert("Please enter a search term", "warning")
      }
    })
  }
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
