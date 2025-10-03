"use client"

import { useEffect, useState } from "react"
import { useRouter } from "next/navigation"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { 
  Home, 
  ConciergeBell, 
  CalendarCheck, 
  Wallet, 
  Star, 
  User, 
  Bell,
  Plus
} from "lucide-react"
import Link from "next/link"

export default function ProviderDashboard() {
  const router = useRouter()
  const [user, setUser] = useState<any>(null)
  const [stats, setStats] = useState({
    activeServices: 0,
    totalBookings: 0,
    avgRating: 0,
    totalEarnings: 0
  })

  useEffect(() => {
    // Check if user is authenticated
    const token = localStorage.getItem("authToken")
    if (!token) {
      router.push("/login")
      return
    }

    // Mock user data - in a real app, this would come from an API
    setUser({
      id: 3,
      name: "Provider User",
      email: "provider@jobsmtaani.com",
      role: "service_provider",
      avatar: "/placeholder-user.jpg"
    })

    // Mock stats data
    setStats({
      activeServices: 8,
      totalBookings: 42,
      avgRating: 4.7,
      totalEarnings: 24500
    })
  }, [router])

  const handleLogout = () => {
    localStorage.removeItem("authToken")
    router.push("/login")
  }

  if (!user) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-primary"></div>
      </div>
    )
  }

  return (
    <div className="flex min-h-screen">
      {/* Sidebar */}
      <div className="w-64 bg-gradient-to-b from-primary to-primary/80 text-white p-6">
        <div className="mb-8">
          <h1 className="text-2xl font-bold">JobsMtaani</h1>
          <p className="text-primary-foreground/80 text-sm">Provider Dashboard</p>
        </div>
        
        <nav className="space-y-2">
          <Link href="/provider" className="flex items-center gap-3 p-3 rounded-lg bg-white/10">
            <Home className="h-5 w-5" />
            <span>Dashboard</span>
          </Link>
          <Link href="/provider/services" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
            <ConciergeBell className="h-5 w-5" />
            <span>My Services</span>
          </Link>
          <Link href="/provider/bookings" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
            <CalendarCheck className="h-5 w-5" />
            <span>Bookings</span>
          </Link>
          <Link href="/provider/earnings" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
            <Wallet className="h-5 w-5" />
            <span>Earnings</span>
          </Link>
          <Link href="/provider/reviews" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
            <Star className="h-5 w-5" />
            <span>Reviews</span>
          </Link>
          <Link href="/provider/profile" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
            <User className="h-5 w-5" />
            <span>Profile</span>
          </Link>
        </nav>
        
        <div className="mt-auto pt-8">
          <Button 
            variant="outline" 
            className="w-full text-white border-white hover:bg-white hover:text-primary"
            onClick={handleLogout}
          >
            Logout
          </Button>
        </div>
      </div>

      {/* Main Content */}
      <div className="flex-1 flex flex-col">
        {/* Header */}
        <header className="border-b p-4 flex items-center justify-between">
          <h2 className="text-2xl font-bold">Provider Dashboard</h2>
          <div className="flex items-center gap-4">
            <Button variant="outline" size="icon">
              <Bell className="h-4 w-4" />
            </Button>
            <div className="flex items-center gap-2">
              <Avatar>
                <AvatarImage src={user.avatar} alt={user.name} />
                <AvatarFallback>{user.name.charAt(0)}</AvatarFallback>
              </Avatar>
              <div>
                <p className="text-sm font-medium">{user.name}</p>
                <p className="text-xs text-muted-foreground">{user.role.replace("_", " ")}</p>
              </div>
            </div>
          </div>
        </header>

        {/* Welcome Section */}
        <main className="flex-1 p-6 bg-gray-50">
          <div className="mb-8">
            <h1 className="text-3xl font-bold">Welcome back, {user.name.split(" ")[0]}!</h1>
            <p className="text-muted-foreground">Manage your services and bookings</p>
          </div>

          {/* Stats Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Active Services</CardTitle>
                <ConciergeBell className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{stats.activeServices}</div>
                <p className="text-xs text-muted-foreground">Services listed</p>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Total Bookings</CardTitle>
                <CalendarCheck className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{stats.totalBookings}</div>
                <p className="text-xs text-muted-foreground">All time bookings</p>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Average Rating</CardTitle>
                <Star className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{stats.avgRating}</div>
                <p className="text-xs text-muted-foreground">From customers</p>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Total Earnings</CardTitle>
                <Wallet className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">KES {stats.totalEarnings.toLocaleString()}</div>
                <p className="text-xs text-muted-foreground">Lifetime earnings</p>
              </CardContent>
            </Card>
          </div>

          {/* Recent Bookings and Quick Actions */}
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <Card className="lg:col-span-2">
              <CardHeader>
                <CardTitle>Recent Bookings</CardTitle>
                <CardDescription>Your latest service bookings</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {[
                    { id: 1, service: "Plumbing Repair", customer: "John Mwangi", date: "Oct 5, 2025", status: "Confirmed", amount: 3500 },
                    { id: 2, service: "House Cleaning", customer: "Sarah Johnson", date: "Oct 3, 2025", status: "Completed", amount: 2800 },
                    { id: 3, service: "Electrical Work", customer: "Michael Ochieng", date: "Oct 1, 2025", status: "In Progress", amount: 4200 },
                  ].map((booking) => (
                    <div key={booking.id} className="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                      <div>
                        <p className="font-medium">{booking.service}</p>
                        <p className="text-sm text-muted-foreground">Customer: {booking.customer}</p>
                        <p className="text-sm text-muted-foreground">{booking.date}</p>
                      </div>
                      <div className="text-right">
                        <Badge 
                          variant={
                            booking.status === "Confirmed" ? "default" : 
                            booking.status === "Completed" ? "secondary" : 
                            "destructive"
                          }
                        >
                          {booking.status}
                        </Badge>
                        <p className="text-sm font-medium mt-1">KES {booking.amount.toLocaleString()}</p>
                      </div>
                    </div>
                  ))}
                </div>
                <Button asChild variant="outline" className="w-full mt-4">
                  <Link href="/provider/bookings">View All Bookings</Link>
                </Button>
              </CardContent>
            </Card>

            <div className="space-y-6">
              <Card>
                <CardHeader>
                  <CardTitle>Quick Actions</CardTitle>
                  <CardDescription>Common tasks you can perform</CardDescription>
                </CardHeader>
                <CardContent className="space-y-3">
                  <Button asChild className="w-full justify-start">
                    <Link href="/provider/services/new">
                      <Plus className="h-4 w-4 mr-2" />
                      Add New Service
                    </Link>
                  </Button>
                  <Button asChild variant="outline" className="w-full justify-start">
                    <Link href="/provider/bookings">
                      <CalendarCheck className="h-4 w-4 mr-2" />
                      View Bookings
                    </Link>
                  </Button>
                  <Button asChild variant="outline" className="w-full justify-start">
                    <Link href="/provider/earnings">
                      <Wallet className="h-4 w-4 mr-2" />
                      View Earnings
                    </Link>
                  </Button>
                  <Button asChild variant="outline" className="w-full justify-start">
                    <Link href="/provider/profile">
                      <User className="h-4 w-4 mr-2" />
                      Edit Profile
                    </Link>
                  </Button>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle>Your Top Services</CardTitle>
                  <CardDescription>Most booked services</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {[
                      { id: 1, name: "Emergency Plumbing", bookings: 12, image: "/plumbing-service.jpg" },
                      { id: 2, name: "House Cleaning", bookings: 8, image: "/house-cleaning.png" },
                      { id: 3, name: "Electrical Work", bookings: 6, image: "/electrical-work.jpg" },
                    ].map((service) => (
                      <div key={service.id} className="flex items-center gap-3">
                        <img 
                          src={service.image} 
                          alt={service.name} 
                          className="w-12 h-12 rounded object-cover"
                        />
                        <div className="flex-1">
                          <p className="font-medium">{service.name}</p>
                          <p className="text-sm text-muted-foreground">{service.bookings} bookings</p>
                        </div>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </main>
      </div>
    </div>
  )
}