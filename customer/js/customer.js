/**
 * Customer Dashboard JavaScript
 * JobsMtaani Platform
 */

class CustomerDashboard {
  constructor() {
    this.currentSection = "dashboard"
    this.init()
  }

  init() {
    this.bindEvents()
    this.loadDashboardData()
    this.loadCategories()
    this.loadProfile()
  }

  bindEvents() {
    // Navigation
    document.querySelectorAll("[data-section]").forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault()
        const section = e.target.closest("[data-section]").dataset.section
        this.showSection(section)
      })
    })

    // Book service form
    const bookServiceForm = document.getElementById("bookServiceForm")
    if (bookServiceForm) {
      bookServiceForm.addEventListener("submit", (e) => {
        e.preventDefault()
        this.bookService()
      })
    }

    // Profile form
    const profileForm = document.getElementById("profileForm")
    if (profileForm) {
      profileForm.addEventListener("submit", (e) => {
        e.preventDefault()
        this.updateProfile()
      })
    }

    // Change password form
    const changePasswordForm = document.getElementById("changePasswordForm")
    if (changePasswordForm) {
      changePasswordForm.addEventListener("submit", (e) => {
        e.preventDefault()
        this.changePassword()
      })
    }

    // Filters
    const categoryFilter = document.getElementById("categoryFilter")
    const priceFilter = document.getElementById("priceFilter")
    const serviceSearch = document.getElementById("serviceSearch")

    if (categoryFilter) categoryFilter.addEventListener("change", () => this.loadServices())
    if (priceFilter) priceFilter.addEventListener("change", () => this.loadServices())
    if (serviceSearch) {
      serviceSearch.addEventListener("input", (e) => {
        clearTimeout(this.searchTimeout)
        this.searchTimeout = setTimeout(() => this.loadServices(), 500)
      })
    }

    // Booking filters
    const statusFilter = document.getElementById("bookingStatusFilter")
    const dateFilter = document.getElementById("bookingDateFilter")
    const bookingSearch = document.getElementById("bookingSearch")

    if (statusFilter) statusFilter.addEventListener("change", () => this.loadBookings())
    if (dateFilter) dateFilter.addEventListener("change", () => this.loadBookings())
    if (bookingSearch) {
      bookingSearch.addEventListener("input", (e) => {
        clearTimeout(this.bookingSearchTimeout)
        this.bookingSearchTimeout = setTimeout(() => this.loadBookings(), 500)
      })
    }
  }

  showSection(sectionName) {
    // Hide all sections
    document.querySelectorAll(".content-section").forEach((section) => {
      section.style.display = "none"
    })

    // Show selected section
    const targetSection = document.getElementById(`${sectionName}-section`)
    if (targetSection) {
      targetSection.style.display = "block"
    }

    // Update navigation
    document.querySelectorAll(".sidebar .nav-link").forEach((link) => {
      link.classList.remove("active")
    })
    document.querySelector(`[data-section="${sectionName}"]`).classList.add("active")

    this.currentSection = sectionName

    // Load section-specific data
    switch (sectionName) {
      case "bookings":
        this.loadBookings()
        break
      case "favorites":
        this.loadFavorites()
        break
      case "browse":
        this.loadServices()
        break
      case "dashboard":
        this.loadDashboardData()
        break
    }
  }

  async loadDashboardData() {
    try {
      // Load recent bookings
      const response = await fetch("../api/bookings.php?action=my-bookings&limit=5")
      const data = await response.json()

      if (data.success) {
        this.renderRecentBookings(data.bookings)
      }
    } catch (error) {
      console.error("Error loading dashboard data:", error)
    }
  }

  renderRecentBookings(bookings) {
    const tbody = document.getElementById("recent-bookings")
    if (!tbody) return

    tbody.innerHTML = bookings
      .map(
        (booking) => `
            <tr>
                <td>${booking.service_title}</td>
                <td>${booking.provider_first_name} ${booking.provider_last_name}</td>
                <td>${new Date(booking.booking_date).toLocaleDateString()}</td>
                <td><span class="badge bg-${this.getStatusColor(booking.status)}">${booking.status}</span></td>
                <td>KES ${Number.parseFloat(booking.total_amount).toLocaleString()}</td>
            </tr>
        `,
      )
      .join("")
  }

  async loadBookings() {
    try {
      const params = new URLSearchParams({
        action: "my-bookings",
      })

      // Add filters
      const statusFilter = document.getElementById("bookingStatusFilter")?.value
      const dateFilter = document.getElementById("bookingDateFilter")?.value
      const searchFilter = document.getElementById("bookingSearch")?.value

      if (statusFilter) params.append("status", statusFilter)
      if (dateFilter) params.append("date", dateFilter)
      if (searchFilter) params.append("search", searchFilter)

      const response = await fetch(`../api/bookings.php?${params}`)
      const data = await response.json()

      if (data.success) {
        this.renderBookings(data.bookings)
      }
    } catch (error) {
      console.error("Error loading bookings:", error)
    }
  }

  renderBookings(bookings) {
    const tbody = document.getElementById("bookings-table")
    if (!tbody) return

    tbody.innerHTML = bookings
      .map(
        (booking) => `
            <tr>
                <td><strong>${booking.booking_number}</strong></td>
                <td>${booking.service_title}</td>
                <td>${booking.provider_first_name} ${booking.provider_last_name}</td>
                <td>
                    ${new Date(booking.booking_date).toLocaleDateString()}<br>
                    <small class="text-muted">${booking.booking_time}</small>
                </td>
                <td>
                    <small class="text-muted">${booking.location_type.replace("_", " ")}</small><br>
                    ${booking.service_address || "N/A"}
                </td>
                <td><span class="badge bg-${this.getStatusColor(booking.status)}">${booking.status}</span></td>
                <td><strong>KES ${Number.parseFloat(booking.total_amount).toLocaleString()}</strong></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        ${this.getBookingActions(booking)}
                    </div>
                </td>
            </tr>
        `,
      )
      .join("")
  }

  getBookingActions(booking) {
    const actions = []

    if (booking.status === "pending") {
      actions.push(`
                <button class="btn btn-outline-danger" onclick="customer.cancelBooking(${booking.id})">
                    <i class="fas fa-times"></i>
                </button>
            `)
    }

    if (booking.status === "completed") {
      actions.push(`
                <button class="btn btn-outline-warning" onclick="customer.reviewService(${booking.id})">
                    <i class="fas fa-star"></i>
                </button>
            `)
    }

    actions.push(`
            <button class="btn btn-outline-info" onclick="customer.viewBookingDetails(${booking.id})">
                <i class="fas fa-eye"></i>
            </button>
        `)

    return actions.join("")
  }

  async loadServices() {
    try {
      const params = new URLSearchParams({
        action: "list",
        limit: 20,
      })

      // Add filters
      const categoryFilter = document.getElementById("categoryFilter")?.value
      const priceFilter = document.getElementById("priceFilter")?.value
      const searchFilter = document.getElementById("serviceSearch")?.value

      if (categoryFilter) params.append("category_id", categoryFilter)
      if (searchFilter) params.append("search", searchFilter)

      const response = await fetch(`../api/services.php?${params}`)
      const data = await response.json()

      if (data.success) {
        let services = data.services

        // Apply price filter
        if (priceFilter) {
          services = this.filterByPrice(services, priceFilter)
        }

        this.renderServices(services)
      }
    } catch (error) {
      console.error("Error loading services:", error)
    }
  }

  filterByPrice(services, priceRange) {
    if (!priceRange) return services

    return services.filter((service) => {
      const price = Number.parseFloat(service.price)
      switch (priceRange) {
        case "0-500":
          return price <= 500
        case "500-1000":
          return price > 500 && price <= 1000
        case "1000-2500":
          return price > 1000 && price <= 2500
        case "2500+":
          return price > 2500
        default:
          return true
      }
    })
  }

  renderServices(services) {
    const grid = document.getElementById("services-grid")
    if (!grid) return

    grid.innerHTML = services
      .map(
        (service) => `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="service-card">
                    <button class="favorite-btn" onclick="customer.toggleFavorite(${service.id})">
                        <i class="fas fa-heart"></i>
                    </button>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">${service.title}</h5>
                            <span class="badge bg-primary">${service.category_name}</span>
                        </div>
                        <p class="text-muted small mb-2">by ${service.first_name} ${service.last_name}</p>
                        <p class="card-text">${service.short_description || service.description.substring(0, 100) + "..."}</p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <strong class="text-primary">KES ${Number.parseFloat(service.price).toLocaleString()}</strong>
                                <small class="text-muted">/${service.price_type}</small>
                            </div>
                            <div class="text-end">
                                <div class="text-warning">
                                    ${"★".repeat(Math.floor(service.avg_rating || 0))}${"☆".repeat(5 - Math.floor(service.avg_rating || 0))}
                                </div>
                                <small class="text-muted">${service.review_count || 0} reviews</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-fill" onclick="customer.openBookingModal(${service.id}, ${service.provider_id}, '${service.title}', ${service.price})">
                                <i class="fas fa-calendar-plus"></i> Book Now
                            </button>
                            <button class="btn btn-outline-info" onclick="customer.viewServiceDetails(${service.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `,
      )
      .join("")
  }

  async loadFavorites() {
    try {
      const response = await fetch("../api/favorites.php")
      const data = await response.json()

      if (data.success) {
        this.renderFavorites(data.favorites)
      }
    } catch (error) {
      console.error("Error loading favorites:", error)
    }
  }

  renderFavorites(favorites) {
    const grid = document.getElementById("favorites-grid")
    if (!grid) return

    if (favorites.length === 0) {
      grid.innerHTML = `
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No favorites yet</h5>
                        <p class="text-muted">Start browsing services to add them to your favorites</p>
                        <button class="btn btn-primary" data-section="browse">Browse Services</button>
                    </div>
                </div>
            `
      return
    }

    grid.innerHTML = favorites
      .map(
        (service) => `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="service-card">
                    <button class="favorite-btn active" onclick="customer.toggleFavorite(${service.id})">
                        <i class="fas fa-heart"></i>
                    </button>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">${service.title}</h5>
                            <span class="badge bg-primary">${service.category_name}</span>
                        </div>
                        <p class="text-muted small mb-2">by ${service.first_name} ${service.last_name}</p>
                        <p class="card-text">${service.short_description || service.description.substring(0, 100) + "..."}</p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <strong class="text-primary">KES ${Number.parseFloat(service.price).toLocaleString()}</strong>
                                <small class="text-muted">/${service.price_type}</small>
                            </div>
                            <div class="text-end">
                                <div class="text-warning">
                                    ${"★".repeat(Math.floor(service.avg_rating || 0))}${"☆".repeat(5 - Math.floor(service.avg_rating || 0))}
                                </div>
                                <small class="text-muted">${service.review_count || 0} reviews</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-fill" onclick="customer.openBookingModal(${service.id}, ${service.provider_id}, '${service.title}', ${service.price})">
                                <i class="fas fa-calendar-plus"></i> Book Now
                            </button>
                            <button class="btn btn-outline-info" onclick="customer.viewServiceDetails(${service.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `,
      )
      .join("")
  }

  async loadCategories() {
    try {
      const response = await fetch("../api/services.php?action=categories")
      const data = await response.json()

      if (data.success) {
        const select = document.getElementById("categoryFilter")
        if (select) {
          select.innerHTML =
            '<option value="">All Categories</option>' +
            data.categories.map((cat) => `<option value="${cat.id}">${cat.name}</option>`).join("")
        }
      }
    } catch (error) {
      console.error("Error loading categories:", error)
    }
  }

  async loadProfile() {
    try {
      const response = await fetch("../api/profile.php")
      const data = await response.json()

      if (data.success && data.profile) {
        const profile = data.profile
        document.getElementById("profilePhone").value = profile.phone || ""
        document.getElementById("profileBio").value = profile.bio || ""
        document.getElementById("profileCity").value = profile.city || ""
        document.getElementById("profileAddress").value = profile.address || ""
      }
    } catch (error) {
      console.error("Error loading profile:", error)
    }
  }

  openBookingModal(serviceId, providerId, serviceTitle, price) {
    document.getElementById("bookingServiceId").value = serviceId
    document.getElementById("bookingProviderId").value = providerId
    document.getElementById("bookingTotalAmount").textContent = `KES ${Number.parseFloat(price).toLocaleString()}`

    const modal = new window.bootstrap.Modal(document.getElementById("bookServiceModal"))
    modal.show()
  }

  async bookService() {
    const form = document.getElementById("bookServiceForm")
    const formData = new FormData(form)
    const bookingData = Object.fromEntries(formData)

    // Add calculated fields
    bookingData.total_amount = document
      .getElementById("bookingTotalAmount")
      .textContent.replace("KES ", "")
      .replace(",", "")
    bookingData.status = "pending"

    try {
      const response = await fetch("../api/bookings.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(bookingData),
      })

      const data = await response.json()

      if (data.success) {
        this.showAlert("Service booked successfully!", "success")
        form.reset()
        const modal = window.bootstrap.Modal.getInstance(document.getElementById("bookServiceModal"))
        modal.hide()
        this.loadBookings()
      } else {
        this.showAlert(data.message || "Failed to book service", "danger")
      }
    } catch (error) {
      console.error("Error booking service:", error)
      this.showAlert("Error booking service", "danger")
    }
  }

  async toggleFavorite(serviceId) {
    try {
      const response = await fetch("../api/favorites.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ service_id: serviceId }),
      })

      const data = await response.json()

      if (data.success) {
        this.showAlert(data.message, "success")
        // Refresh current section
        if (this.currentSection === "favorites") {
          this.loadFavorites()
        }
      } else {
        this.showAlert(data.message || "Failed to update favorite", "danger")
      }
    } catch (error) {
      console.error("Error toggling favorite:", error)
      this.showAlert("Error updating favorite", "danger")
    }
  }

  async updateProfile() {
    const form = document.getElementById("profileForm")
    const formData = new FormData(form)
    const profileData = Object.fromEntries(formData)

    try {
      const response = await fetch("../api/profile.php", {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(profileData),
      })

      const data = await response.json()

      if (data.success) {
        this.showAlert("Profile updated successfully!", "success")
      } else {
        this.showAlert(data.message || "Failed to update profile", "danger")
      }
    } catch (error) {
      console.error("Error updating profile:", error)
      this.showAlert("Error updating profile", "danger")
    }
  }

  async changePassword() {
    const form = document.getElementById("changePasswordForm")
    const formData = new FormData(form)
    const passwordData = Object.fromEntries(formData)

    if (passwordData.new_password !== passwordData.confirm_password) {
      this.showAlert("New passwords do not match", "danger")
      return
    }

    try {
      const response = await fetch("../api/auth.php?action=change-password", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(passwordData),
      })

      const data = await response.json()

      if (data.success) {
        this.showAlert("Password changed successfully!", "success")
        form.reset()
        const modal = window.bootstrap.Modal.getInstance(document.getElementById("changePasswordModal"))
        modal.hide()
      } else {
        this.showAlert(data.message || "Failed to change password", "danger")
      }
    } catch (error) {
      console.error("Error changing password:", error)
      this.showAlert("Error changing password", "danger")
    }
  }

  getStatusColor(status) {
    const colors = {
      pending: "warning",
      confirmed: "info",
      in_progress: "primary",
      completed: "success",
      cancelled: "danger",
    }
    return colors[status] || "secondary"
  }

  showAlert(message, type = "info") {
    const alert = document.createElement("div")
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`
    alert.style.cssText = "top: 20px; right: 20px; z-index: 9999; min-width: 300px;"
    alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `

    document.body.appendChild(alert)

    setTimeout(() => {
      if (alert.parentNode) {
        alert.remove()
      }
    }, 5000)
  }

  // Placeholder methods
  cancelBooking(bookingId) {
    if (confirm("Are you sure you want to cancel this booking?")) {
      console.log("Cancel booking:", bookingId)
    }
  }

  reviewService(bookingId) {
    console.log("Review service:", bookingId)
  }

  viewBookingDetails(bookingId) {
    console.log("View booking details:", bookingId)
  }

  viewServiceDetails(serviceId) {
    console.log("View service details:", serviceId)
  }
}

// Initialize customer dashboard
const customer = new CustomerDashboard()
