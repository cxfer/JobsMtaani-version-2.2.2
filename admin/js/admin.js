import { Chart } from "@/components/ui/chart"
/**
 * Superadmin Dashboard JavaScript
 * JobsMtaani Platform
 */

class AdminDashboard {
  constructor() {
    this.currentSection = "dashboard"
    this.init()
  }

  init() {
    this.bindEvents()
    this.loadDashboardData()
    this.updateColors()
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

    // Settings form
    const settingsForm = document.getElementById("settingsForm")
    if (settingsForm) {
      settingsForm.addEventListener("submit", (e) => {
        e.preventDefault()
        this.saveSettings()
      })
    }

    // Add user form
    const addUserForm = document.getElementById("addUserForm")
    if (addUserForm) {
      addUserForm.addEventListener("submit", (e) => {
        e.preventDefault()
        this.addUser()
      })
    }

    const addServiceForm = document.getElementById("addServiceForm")
    if (addServiceForm) {
      addServiceForm.addEventListener("submit", (e) => {
        e.preventDefault()
        this.addService()
      })
    }

    const addCategoryForm = document.getElementById("addCategoryForm")
    if (addCategoryForm) {
      addCategoryForm.addEventListener("submit", (e) => {
        e.preventDefault()
        this.addCategory()
      })
    }

    // Color inputs - update CSS variables in real-time
    document.querySelectorAll('input[type="color"]').forEach((input) => {
      input.addEventListener("change", (e) => {
        this.updateColors()
      })
    })

    const userSearch = document.getElementById("userSearch")
    if (userSearch) {
      userSearch.addEventListener("input", (e) => {
        this.searchUsers(e.target.value)
      })
    }

    const serviceSearch = document.getElementById("serviceSearch")
    if (serviceSearch) {
      serviceSearch.addEventListener("input", (e) => {
        this.searchServices(e.target.value)
      })
    }

    const bookingSearch = document.getElementById("bookingSearch")
    if (bookingSearch) {
      bookingSearch.addEventListener("input", (e) => {
        this.searchBookings(e.target.value)
      })
    }

    const paymentSearch = document.getElementById("paymentSearch")
    if (paymentSearch) {
      paymentSearch.addEventListener("input", (e) => {
        this.searchPayments(e.target.value)
      })
    }

    const reviewSearch = document.getElementById("reviewSearch")
    if (reviewSearch) {
      reviewSearch.addEventListener("input", (e) => {
        this.searchReviews(e.target.value)
      })
    }

    const serviceStatusFilter = document.getElementById("serviceStatusFilter")
    if (serviceStatusFilter) {
      serviceStatusFilter.addEventListener("change", (e) => {
        this.filterServices(e.target.value)
      })
    }

    const bookingStatusFilter = document.getElementById("bookingStatusFilter")
    if (bookingStatusFilter) {
      bookingStatusFilter.addEventListener("change", (e) => {
        this.filterBookings(e.target.value)
      })
    }

    const paymentStatusFilter = document.getElementById("paymentStatusFilter")
    if (paymentStatusFilter) {
      paymentStatusFilter.addEventListener("change", (e) => {
        this.filterPayments(e.target.value)
      })
    }

    const reviewRatingFilter = document.getElementById("reviewRatingFilter")
    if (reviewRatingFilter) {
      reviewRatingFilter.addEventListener("change", (e) => {
        this.filterReviews(e.target.value)
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
      case "users":
        this.loadUsers()
        break
      case "services":
        this.loadServices()
        break
      case "bookings":
        this.loadBookings()
        break
      case "categories":
        this.loadCategories()
        break
      case "payments":
        this.loadPayments()
        break
      case "reviews":
        this.loadReviews()
        break
      case "analytics":
        this.loadAnalytics()
        break
      case "dashboard":
        this.loadDashboardData()
        break
    }
  }

  async loadDashboardData() {
    try {
      // Load recent bookings
      const response = await fetch("../api/bookings.php?action=recent&limit=10")
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

  async loadUsers() {
    try {
      const response = await fetch("../api/users.php?action=list")
      const data = await response.json()

      if (data.success) {
        this.renderUsers(data.users)
      }
    } catch (error) {
      console.error("Error loading users:", error)
    }
  }

  renderUsers(users) {
    const tbody = document.getElementById("users-table")
    if (!tbody) return

    tbody.innerHTML = users
      .map(
        (user) => `
            <tr>
                <td>${user.id}</td>
                <td>${user.first_name} ${user.last_name}</td>
                <td>${user.email}</td>
                <td><span class="badge bg-${this.getUserTypeColor(user.user_type)}">${user.user_type}</span></td>
                <td><span class="badge bg-${this.getStatusColor(user.status)}">${user.status}</span></td>
                <td>${new Date(user.created_at).toLocaleDateString()}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="admin.editUser(${user.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="admin.deleteUser(${user.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `,
      )
      .join("")
  }

  async loadServices() {
    try {
      const response = await fetch("../api/services.php?action=list")
      const data = await response.json()

      if (data.success) {
        this.renderServices(data.services)
        this.loadServiceProviders()
        this.loadServiceCategories()
      }
    } catch (error) {
      console.error("Error loading services:", error)
    }
  }

  renderServices(services) {
    const tbody = document.getElementById("services-table")
    if (!tbody) return

    tbody.innerHTML = services
      .map(
        (service) => `
            <tr>
                <td>${service.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="${service.image_url || "/placeholder.svg?height=40&width=40"}" 
                             class="rounded me-2" width="40" height="40" alt="${service.title}">
                        <div>
                            <strong>${service.title}</strong>
                            <br><small class="text-muted">${service.description.substring(0, 50)}...</small>
                        </div>
                    </div>
                </td>
                <td>${service.provider_name}</td>
                <td>${service.category_name}</td>
                <td>KES ${Number.parseFloat(service.price).toLocaleString()}</td>
                <td><span class="badge bg-${this.getStatusColor(service.status)}">${service.status}</span></td>
                <td>${new Date(service.created_at).toLocaleDateString()}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="admin.editService(${service.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-${service.is_active ? "warning" : "success"}" 
                                onclick="admin.toggleService(${service.id}, ${service.is_active})">
                            <i class="fas fa-${service.is_active ? "pause" : "play"}"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="admin.deleteService(${service.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `,
      )
      .join("")
  }

  async loadBookings() {
    try {
      const response = await fetch("../api/bookings.php?action=list")
      const data = await response.json()

      if (data.success) {
        this.renderBookings(data.bookings)
        this.updateBookingStats(data.stats)
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
                <td>${booking.customer_name}</td>
                <td>${booking.service_title}</td>
                <td>${booking.provider_name}</td>
                <td>
                    ${new Date(booking.booking_date).toLocaleDateString()}<br>
                    <small class="text-muted">${booking.booking_time}</small>
                </td>
                <td>KES ${Number.parseFloat(booking.total_amount).toLocaleString()}</td>
                <td><span class="badge bg-${this.getStatusColor(booking.status)}">${booking.status}</span></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info" onclick="admin.viewBooking(${booking.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-primary" onclick="admin.updateBookingStatus(${booking.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `,
      )
      .join("")
  }

  updateBookingStats(stats) {
    document.getElementById("pending-bookings-count").textContent = stats.pending || 0
    document.getElementById("confirmed-bookings-count").textContent = stats.confirmed || 0
    document.getElementById("completed-bookings-count").textContent = stats.completed || 0
    document.getElementById("cancelled-bookings-count").textContent = stats.cancelled || 0
  }

  async loadCategories() {
    try {
      const response = await fetch("../api/categories.php?action=list")
      const data = await response.json()

      if (data.success) {
        this.renderCategories(data.categories)
      }
    } catch (error) {
      console.error("Error loading categories:", error)
    }
  }

  renderCategories(categories) {
    const tbody = document.getElementById("categories-table")
    if (!tbody) return

    tbody.innerHTML = categories
      .map(
        (category) => `
            <tr>
                <td>${category.id}</td>
                <td><i class="${category.icon} text-primary"></i></td>
                <td><strong>${category.name}</strong></td>
                <td>${category.description || "No description"}</td>
                <td>${category.services_count || 0}</td>
                <td><span class="badge bg-${category.is_active ? "success" : "secondary"}">${category.is_active ? "Active" : "Inactive"}</span></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="admin.editCategory(${category.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="admin.deleteCategory(${category.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `,
      )
      .join("")
  }

  async loadPayments() {
    try {
      const response = await fetch("../api/payments.php?action=list")
      const data = await response.json()

      if (data.success) {
        this.renderPayments(data.payments)
        this.updatePaymentStats(data.stats)
      }
    } catch (error) {
      console.error("Error loading payments:", error)
    }
  }

  renderPayments(payments) {
    const tbody = document.getElementById("payments-table")
    if (!tbody) return

    tbody.innerHTML = payments
      .map(
        (payment) => `
            <tr>
                <td><strong>${payment.transaction_id}</strong></td>
                <td>${payment.booking_number}</td>
                <td>${payment.customer_name}</td>
                <td>KES ${Number.parseFloat(payment.amount).toLocaleString()}</td>
                <td>KES ${Number.parseFloat(payment.commission).toLocaleString()}</td>
                <td>${payment.payment_method}</td>
                <td><span class="badge bg-${this.getStatusColor(payment.status)}">${payment.status}</span></td>
                <td>${new Date(payment.created_at).toLocaleDateString()}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info" onclick="admin.viewPayment('${payment.transaction_id}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${
                          payment.status === "completed"
                            ? `<button class="btn btn-outline-warning" onclick="admin.refundPayment('${payment.transaction_id}')">
                            <i class="fas fa-undo"></i>
                          </button>`
                            : ""
                        }
                    </div>
                </td>
            </tr>
        `,
      )
      .join("")
  }

  updatePaymentStats(stats) {
    document.getElementById("total-revenue").textContent =
      `KES ${Number.parseFloat(stats.total_revenue || 0).toLocaleString()}`
    document.getElementById("pending-payments").textContent = stats.pending_payments || 0
    document.getElementById("commission-earned").textContent =
      `KES ${Number.parseFloat(stats.commission_earned || 0).toLocaleString()}`
  }

  async loadReviews() {
    try {
      const response = await fetch("../api/reviews.php?action=list")
      const data = await response.json()

      if (data.success) {
        this.renderReviews(data.reviews)
      }
    } catch (error) {
      console.error("Error loading reviews:", error)
    }
  }

  renderReviews(reviews) {
    const tbody = document.getElementById("reviews-table")
    if (!tbody) return

    tbody.innerHTML = reviews
      .map(
        (review) => `
            <tr>
                <td>${review.id}</td>
                <td>${review.customer_name}</td>
                <td>${review.service_title}</td>
                <td>${review.provider_name}</td>
                <td>
                    <div class="text-warning">
                        ${"★".repeat(review.rating)}${"☆".repeat(5 - review.rating)}
                    </div>
                </td>
                <td>
                    <div style="max-width: 200px;">
                        ${review.comment ? review.comment.substring(0, 100) + (review.comment.length > 100 ? "..." : "") : "No comment"}
                    </div>
                </td>
                <td>${new Date(review.created_at).toLocaleDateString()}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info" onclick="admin.viewReview(${review.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="admin.deleteReview(${review.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `,
      )
      .join("")
  }

  async loadAnalytics() {
    try {
      const response = await fetch("../api/analytics.php")
      const data = await response.json()

      if (data.success) {
        this.renderRevenueChart(data.revenue_data)
        this.renderCategoriesChart(data.categories_data)
        this.renderTopProviders(data.top_providers)
        this.renderActivityFeed(data.recent_activity)
      }
    } catch (error) {
      console.error("Error loading analytics:", error)
    }
  }

  renderRevenueChart(revenueData) {
    const ctx = document.getElementById("revenueChart")
    if (!ctx) return

    new Chart(ctx, {
      type: "line",
      data: {
        labels: revenueData.labels,
        datasets: [
          {
            label: "Revenue (KES)",
            data: revenueData.values,
            borderColor: "rgb(75, 192, 192)",
            backgroundColor: "rgba(75, 192, 192, 0.1)",
            tension: 0.1,
          },
        ],
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: "Monthly Revenue Trend",
          },
        },
      },
    })
  }

  renderCategoriesChart(categoriesData) {
    const ctx = document.getElementById("categoriesChart")
    if (!ctx) return

    new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: categoriesData.labels,
        datasets: [
          {
            data: categoriesData.values,
            backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF", "#FF9F40"],
          },
        ],
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: "Services by Category",
          },
        },
      },
    })
  }

  renderTopProviders(providers) {
    const tbody = document.getElementById("top-providers-table")
    if (!tbody) return

    tbody.innerHTML = providers
      .map(
        (provider) => `
            <tr>
                <td>${provider.name}</td>
                <td>${provider.services_count}</td>
                <td>${provider.bookings_count}</td>
                <td>KES ${Number.parseFloat(provider.revenue).toLocaleString()}</td>
                <td>
                    <div class="text-warning">
                        ${"★".repeat(Math.floor(provider.rating))}${"☆".repeat(5 - Math.floor(provider.rating))}
                        <small>(${provider.rating})</small>
                    </div>
                </td>
            </tr>
        `,
      )
      .join("")
  }

  renderActivityFeed(activities) {
    const container = document.getElementById("activity-feed")
    if (!container) return

    container.innerHTML = activities
      .map(
        (activity) => `
            <div class="d-flex align-items-start mb-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-${this.getActivityIcon(activity.type)} text-primary"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="fw-bold">${activity.title}</div>
                    <div class="text-muted small">${activity.description}</div>
                    <div class="text-muted small">${new Date(activity.created_at).toLocaleString()}</div>
                </div>
            </div>
        `,
      )
      .join("")
  }

  getActivityIcon(type) {
    const icons = {
      booking: "calendar-check",
      payment: "credit-card",
      user: "user-plus",
      service: "concierge-bell",
      review: "star",
    }
    return icons[type] || "info-circle"
  }

  async addUser() {
    const form = document.getElementById("addUserForm")
    const formData = new FormData(form)
    const userData = Object.fromEntries(formData)

    try {
      const response = await fetch("../api/auth.php?action=register", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(userData),
      })

      const data = await response.json()

      if (data.success) {
        this.showAlert("User added successfully!", "success")
        form.reset()
        const addUserModal = document.getElementById("addUserModal")
        if (addUserModal) {
          const modalInstance = new bootstrap.Modal(addUserModal)
          modalInstance.hide()
        }
        this.loadUsers()
      } else {
        this.showAlert(data.message || "Failed to add user", "danger")
      }
    } catch (error) {
      console.error("Error adding user:", error)
      this.showAlert("Error adding user", "danger")
    }
  }

  async addService() {
    const form = document.getElementById("addServiceForm")
    const formData = new FormData(form)
    const serviceData = Object.fromEntries(formData)

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
          bootstrap.Modal.getInstance(modal).hide()
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

  async addCategory() {
    const form = document.getElementById("addCategoryForm")
    const formData = new FormData(form)
    const categoryData = Object.fromEntries(formData)

    try {
      const response = await fetch("../api/categories.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(categoryData),
      })

      const data = await response.json()

      if (data.success) {
        this.showAlert("Category added successfully!", "success")
        form.reset()
        const modal = document.getElementById("addCategoryModal")
        if (modal) {
          bootstrap.Modal.getInstance(modal).hide()
        }
        this.loadCategories()
      } else {
        this.showAlert(data.message || "Failed to add category", "danger")
      }
    } catch (error) {
      console.error("Error adding category:", error)
      this.showAlert("Error adding category", "danger")
    }
  }

  async loadServiceProviders() {
    try {
      const response = await fetch("../api/users.php?action=providers")
      const data = await response.json()

      if (data.success) {
        const select = document.querySelector('[name="provider_id"]')
        if (select) {
          select.innerHTML =
            '<option value="">Select Provider</option>' +
            data.providers.map((p) => `<option value="${p.id}">${p.first_name} ${p.last_name}</option>`).join("")
        }
      }
    } catch (error) {
      console.error("Error loading providers:", error)
    }
  }

  async loadServiceCategories() {
    try {
      const response = await fetch("../api/categories.php?action=active")
      const data = await response.json()

      if (data.success) {
        const select = document.querySelector('[name="category_id"]')
        if (select) {
          select.innerHTML =
            '<option value="">Select Category</option>' +
            data.categories.map((c) => `<option value="${c.id}">${c.name}</option>`).join("")
        }
      }
    } catch (error) {
      console.error("Error loading categories:", error)
    }
  }

  async saveSettings() {
    const form = document.getElementById("settingsForm")
    const formData = new FormData(form)
    const settings = {}

    // Convert form data to settings object
    for (const [key, value] of formData.entries()) {
      if (form.querySelector(`[name="${key}"]`).type === "checkbox") {
        settings[key] = form.querySelector(`[name="${key}"]`).checked ? "true" : "false"
      } else {
        settings[key] = value
      }
    }

    try {
      const response = await fetch("../api/settings.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(settings),
      })

      const data = await response.json()

      if (data.success) {
        this.showAlert("Settings saved successfully!", "success")
        this.updateColors()
        // Reload page to apply new settings
        setTimeout(() => {
          window.location.reload()
        }, 1000)
      } else {
        this.showAlert(data.message || "Failed to save settings", "danger")
      }
    } catch (error) {
      console.error("Error saving settings:", error)
      this.showAlert("Error saving settings", "danger")
    }
  }

  updateColors() {
    const primaryColor = document.querySelector('[name="primary_color"]')?.value || "#007bff"
    const secondaryColor = document.querySelector('[name="secondary_color"]')?.value || "#6c757d"
    const accentColor = document.querySelector('[name="accent_color"]')?.value || "#28a745"

    document.documentElement.style.setProperty("--primary-color", primaryColor)
    document.documentElement.style.setProperty("--secondary-color", secondaryColor)
    document.documentElement.style.setProperty("--accent-color", accentColor)
  }

  searchUsers(query) {
    const rows = document.querySelectorAll("#users-table tr")
    rows.forEach((row) => {
      const text = row.textContent.toLowerCase()
      row.style.display = text.includes(query.toLowerCase()) ? "" : "none"
    })
  }

  searchServices(query) {
    const rows = document.querySelectorAll("#services-table tr")
    rows.forEach((row) => {
      const text = row.textContent.toLowerCase()
      row.style.display = text.includes(query.toLowerCase()) ? "" : "none"
    })
  }

  searchBookings(query) {
    const rows = document.querySelectorAll("#bookings-table tr")
    rows.forEach((row) => {
      const text = row.textContent.toLowerCase()
      row.style.display = text.includes(query.toLowerCase()) ? "" : "none"
    })
  }

  searchPayments(query) {
    const rows = document.querySelectorAll("#payments-table tr")
    rows.forEach((row) => {
      const text = row.textContent.toLowerCase()
      row.style.display = text.includes(query.toLowerCase()) ? "" : "none"
    })
  }

  searchReviews(query) {
    const rows = document.querySelectorAll("#reviews-table tr")
    rows.forEach((row) => {
      const text = row.textContent.toLowerCase()
      row.style.display = text.includes(query.toLowerCase()) ? "" : "none"
    })
  }

  filterServices(status) {
    const rows = document.querySelectorAll("#services-table tr")
    rows.forEach((row) => {
      if (!status) {
        row.style.display = ""
        return
      }
      const statusBadge = row.querySelector(".badge")
      const rowStatus = statusBadge ? statusBadge.textContent.toLowerCase() : ""
      row.style.display = rowStatus.includes(status.toLowerCase()) ? "" : "none"
    })
  }

  filterBookings(status) {
    const rows = document.querySelectorAll("#bookings-table tr")
    rows.forEach((row) => {
      if (!status) {
        row.style.display = ""
        return
      }
      const statusBadge = row.querySelector(".badge")
      const rowStatus = statusBadge ? statusBadge.textContent.toLowerCase() : ""
      row.style.display = rowStatus.includes(status.toLowerCase()) ? "" : "none"
    })
  }

  filterPayments(status) {
    const rows = document.querySelectorAll("#payments-table tr")
    rows.forEach((row) => {
      if (!status) {
        row.style.display = ""
        return
      }
      const statusBadge = row.querySelector(".badge")
      const rowStatus = statusBadge ? statusBadge.textContent.toLowerCase() : ""
      row.style.display = rowStatus.includes(status.toLowerCase()) ? "" : "none"
    })
  }

  filterReviews(rating) {
    const rows = document.querySelectorAll("#reviews-table tr")
    rows.forEach((row) => {
      if (!rating) {
        row.style.display = ""
        return
      }
      const stars = row.querySelectorAll(".text-warning")[0]
      if (stars) {
        const starCount = (stars.textContent.match(/★/g) || []).length
        row.style.display = starCount == rating ? "" : "none"
      }
    })
  }

  getStatusColor(status) {
    const colors = {
      active: "success",
      inactive: "secondary",
      suspended: "danger",
      pending: "warning",
      confirmed: "info",
      completed: "success",
      cancelled: "danger",
      failed: "danger",
      refunded: "warning",
    }
    return colors[status] || "secondary"
  }

  getUserTypeColor(userType) {
    const colors = {
      customer: "primary",
      service_provider: "success",
      admin: "warning",
      superadmin: "danger",
    }
    return colors[userType] || "secondary"
  }

  showAlert(message, type = "info") {
    // Create alert element
    const alert = document.createElement("div")
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`
    alert.style.cssText = "top: 20px; right: 20px; z-index: 9999; min-width: 300px;"
    alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `

    document.body.appendChild(alert)

    // Auto remove after 5 seconds
    setTimeout(() => {
      if (alert.parentNode) {
        alert.remove()
      }
    }, 5000)
  }

  editUser(userId) {
    console.log("Edit user:", userId)
    // TODO: Implement edit user modal
  }

  deleteUser(userId) {
    if (confirm("Are you sure you want to delete this user?")) {
      this.performDelete("../api/users.php", userId, "user")
    }
  }

  editService(serviceId) {
    console.log("Edit service:", serviceId)
    // TODO: Implement edit service modal
  }

  toggleService(serviceId, isActive) {
    const action = isActive ? "deactivate" : "activate"
    if (confirm(`Are you sure you want to ${action} this service?`)) {
      this.performToggle("../api/services.php", serviceId, action)
    }
  }

  deleteService(serviceId) {
    if (confirm("Are you sure you want to delete this service?")) {
      this.performDelete("../api/services.php", serviceId, "service")
    }
  }

  editCategory(categoryId) {
    console.log("Edit category:", categoryId)
    // TODO: Implement edit category modal
  }

  deleteCategory(categoryId) {
    if (confirm("Are you sure you want to delete this category?")) {
      this.performDelete("../api/categories.php", categoryId, "category")
    }
  }

  viewBooking(bookingId) {
    console.log("View booking:", bookingId)
    // TODO: Implement booking details modal
  }

  updateBookingStatus(bookingId) {
    console.log("Update booking status:", bookingId)
    // TODO: Implement status update modal
  }

  viewPayment(transactionId) {
    console.log("View payment:", transactionId)
    // TODO: Implement payment details modal
  }

  refundPayment(transactionId) {
    if (confirm("Are you sure you want to refund this payment?")) {
      console.log("Refund payment:", transactionId)
      // TODO: Implement refund functionality
    }
  }

  viewReview(reviewId) {
    console.log("View review:", reviewId)
    // TODO: Implement review details modal
  }

  deleteReview(reviewId) {
    if (confirm("Are you sure you want to delete this review?")) {
      this.performDelete("../api/reviews.php", reviewId, "review")
    }
  }

  async performDelete(endpoint, id, type) {
    try {
      const response = await fetch(`${endpoint}?action=delete&id=${id}`, {
        method: "DELETE",
      })
      const data = await response.json()

      if (data.success) {
        this.showAlert(`${type} deleted successfully!`, "success")
        // Reload current section
        this.showSection(this.currentSection)
      } else {
        this.showAlert(data.message || `Failed to delete ${type}`, "danger")
      }
    } catch (error) {
      console.error(`Error deleting ${type}:`, error)
      this.showAlert(`Error deleting ${type}`, "danger")
    }
  }

  async performToggle(endpoint, id, action) {
    try {
      const response = await fetch(`${endpoint}?action=${action}&id=${id}`, {
        method: "PUT",
      })
      const data = await response.json()

      if (data.success) {
        this.showAlert(`Service ${action}d successfully!`, "success")
        this.loadServices()
      } else {
        this.showAlert(data.message || `Failed to ${action} service`, "danger")
      }
    } catch (error) {
      console.error(`Error ${action}ing service:`, error)
      this.showAlert(`Error ${action}ing service`, "danger")
    }
  }
}

// Initialize admin dashboard
const admin = new AdminDashboard()

// Import Bootstrap
const bootstrap = window.bootstrap

if (typeof Chart !== "undefined") {
  Chart.defaults.responsive = true
  Chart.defaults.maintainAspectRatio = false
}
