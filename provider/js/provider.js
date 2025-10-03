/**
 * Service Provider Dashboard JavaScript
 * JobsMtaani Platform
 */

class ProviderDashboard {
  constructor() {
    this.currentSection = "dashboard"
    this.init()
  }

  init() {
    this.bindEvents()
    this.loadDashboardData()
    this.loadCategories()
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

    // Add service form
    const addServiceForm = document.getElementById("addServiceForm")
    if (addServiceForm) {
      addServiceForm.addEventListener("submit", (e) => {
        e.preventDefault()
        this.addService()
      })
    }

    // Booking filters
    const statusFilter = document.getElementById("bookingStatusFilter")
    const dateFilter = document.getElementById("bookingDateFilter")
    const searchFilter = document.getElementById("bookingSearch")

    if (statusFilter) statusFilter.addEventListener("change", () => this.loadBookings())
    if (dateFilter) dateFilter.addEventListener("change", () => this.loadBookings())
    if (searchFilter) {
      searchFilter.addEventListener("input", (e) => {
        clearTimeout(this.searchTimeout)
        this.searchTimeout = setTimeout(() => this.loadBookings(), 500)
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
      case "services":
        this.loadServices()
        break
      case "bookings":
        this.loadBookings()
        break
      case "availability":
        this.loadAvailability()
        break
      case "earnings":
        this.loadEarnings()
        break
      case "dashboard":
        this.loadDashboardData()
        break
    }
  }

  async loadDashboardData() {
    try {
      // Load recent bookings
      const response = await fetch("../api/bookings.php?action=my-bookings&limit=10")
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
                <td><strong>${booking.booking_number}</strong></td>
                <td>${booking.customer_first_name} ${booking.customer_last_name}</td>
                <td>${booking.service_title}</td>
                <td>${new Date(booking.booking_date).toLocaleDateString()}</td>
                <td><span class="badge bg-${this.getStatusColor(booking.status)}">${booking.status}</span></td>
                <td>KES ${Number.parseFloat(booking.total_amount).toLocaleString()}</td>
            </tr>
        `,
      )
      .join("")
  }

  async loadServices() {
    try {
      const response = await fetch("../api/services.php?action=my-services")
      const data = await response.json()

      if (data.success) {
        this.renderServices(data.services)
      }
    } catch (error) {
      console.error("Error loading services:", error)
    }
  }

  renderServices(services) {
    const grid = document.getElementById("services-grid")
    if (!grid) return

    grid.innerHTML = services
      .map(
        (service) => `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="service-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">${service.title}</h5>
                            <span class="badge bg-${service.is_active ? "success" : "secondary"}">
                                ${service.is_active ? "Active" : "Inactive"}
                            </span>
                        </div>
                        <p class="text-muted small mb-2">${service.category_name}</p>
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
                            <button class="btn btn-outline-primary btn-sm flex-fill" onclick="provider.editService(${service.id})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-outline-${service.is_active ? "warning" : "success"} btn-sm" 
                                    onclick="provider.toggleService(${service.id}, ${!service.is_active})">
                                <i class="fas fa-${service.is_active ? "pause" : "play"}"></i> 
                                ${service.is_active ? "Deactivate" : "Activate"}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
                <td>${booking.customer_first_name} ${booking.customer_last_name}</td>
                <td>${booking.service_title}</td>
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
                <button class="btn btn-outline-success" onclick="provider.updateBookingStatus(${booking.id}, 'confirmed')">
                    <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-outline-danger" onclick="provider.updateBookingStatus(${booking.id}, 'cancelled')">
                    <i class="fas fa-times"></i>
                </button>
            `)
    } else if (booking.status === "confirmed") {
      actions.push(`
                <button class="btn btn-outline-primary" onclick="provider.updateBookingStatus(${booking.id}, 'in_progress')">
                    <i class="fas fa-play"></i>
                </button>
            `)
    } else if (booking.status === "in_progress") {
      actions.push(`
                <button class="btn btn-outline-success" onclick="provider.updateBookingStatus(${booking.id}, 'completed')">
                    <i class="fas fa-check-circle"></i>
                </button>
            `)
    }

    actions.push(`
            <button class="btn btn-outline-info" onclick="provider.viewBookingDetails(${booking.id})">
                <i class="fas fa-eye"></i>
            </button>
        `)

    return actions.join("")
  }

  async loadAvailability() {
    const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]
    const grid = document.getElementById("availability-grid")
    if (!grid) return

    try {
      const response = await fetch("../api/availability.php")
      const data = await response.json()
      const availability = data.success ? data.availability : []

      grid.innerHTML = days
        .map((day, index) => {
          const dayAvailability = availability.find((a) => a.day_of_week == index) || {
            day_of_week: index,
            start_time: "09:00",
            end_time: "17:00",
            is_available: false,
          }

          return `
                    <div class="day-card">
                        <h6 class="fw-bold mb-3">${day}</h6>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" 
                                   id="available_${index}" 
                                   ${dayAvailability.is_available ? "checked" : ""}
                                   onchange="provider.updateAvailability(${index})">
                            <label class="form-check-label" for="available_${index}">
                                Available
                            </label>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Start Time</label>
                            <input type="time" class="form-control form-control-sm" 
                                   id="start_${index}" value="${dayAvailability.start_time}"
                                   ${!dayAvailability.is_available ? "disabled" : ""}
                                   onchange="provider.updateAvailability(${index})">
                        </div>
                        <div>
                            <label class="form-label small">End Time</label>
                            <input type="time" class="form-control form-control-sm" 
                                   id="end_${index}" value="${dayAvailability.end_time}"
                                   ${!dayAvailability.is_available ? "disabled" : ""}
                                   onchange="provider.updateAvailability(${index})">
                        </div>
                    </div>
                `
        })
        .join("")
    } catch (error) {
      console.error("Error loading availability:", error)
    }
  }

  async loadCategories() {
    try {
      const response = await fetch("../api/services.php?action=categories")
      const data = await response.json()

      if (data.success) {
        const select = document.getElementById("serviceCategory")
        if (select) {
          select.innerHTML = data.categories.map((cat) => `<option value="${cat.id}">${cat.name}</option>`).join("")
        }
      }
    } catch (error) {
      console.error("Error loading categories:", error)
    }
  }

  async addService() {
    const form = document.getElementById("addServiceForm")
    const formData = new FormData(form)
    const serviceData = Object.fromEntries(formData)

    // Convert checkbox to boolean
    serviceData.is_active = form.querySelector('[name="is_active"]').checked

    try {
      const response = await fetch("../api/services.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(serviceData),
      })

      const data = await response.json()

      if (data.success) {
        this.showAlert("Service added successfully!", "success")
        form.reset()
        const modal = document.getElementById("addServiceModal")
        if (modal) {
          const modalInstance = window.bootstrap.Modal.getInstance(modal)
          modalInstance.hide()
        }
        this.loadServices()
      } else {
        this.showAlert(data.message || "Failed to add service", "danger")
      }
    } catch (error) {
      console.error("Error adding service:", error)
      this.showAlert("Error adding service", "danger")
    }
  }

  async updateBookingStatus(bookingId, status) {
    try {
      const response = await fetch(`../api/bookings.php?action=update-status&id=${bookingId}`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ status }),
      })

      const data = await response.json()

      if (data.success) {
        this.showAlert("Booking status updated successfully!", "success")
        this.loadBookings()
      } else {
        this.showAlert(data.message || "Failed to update booking status", "danger")
      }
    } catch (error) {
      console.error("Error updating booking status:", error)
      this.showAlert("Error updating booking status", "danger")
    }
  }

  async updateAvailability(dayIndex) {
    const isAvailable = document.getElementById(`available_${dayIndex}`).checked
    const startTime = document.getElementById(`start_${dayIndex}`).value
    const endTime = document.getElementById(`end_${dayIndex}`).value

    // Enable/disable time inputs
    document.getElementById(`start_${dayIndex}`).disabled = !isAvailable
    document.getElementById(`end_${dayIndex}`).disabled = !isAvailable

    const availabilityData = {
      day_of_week: dayIndex,
      start_time: startTime,
      end_time: endTime,
      is_available: isAvailable,
    }

    try {
      const response = await fetch("../api/availability.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(availabilityData),
      })

      const data = await response.json()

      if (data.success) {
        this.showAlert("Availability updated successfully!", "success")
      } else {
        this.showAlert(data.message || "Failed to update availability", "danger")
      }
    } catch (error) {
      console.error("Error updating availability:", error)
      this.showAlert("Error updating availability", "danger")
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
  editService(serviceId) {
    console.log("Edit service:", serviceId)
  }

  toggleService(serviceId, activate) {
    console.log("Toggle service:", serviceId, activate)
  }

  viewBookingDetails(bookingId) {
    console.log("View booking details:", bookingId)
  }

  loadEarnings() {
    console.log("Loading earnings...")
  }
}

// Initialize provider dashboard
const provider = new ProviderDashboard()
