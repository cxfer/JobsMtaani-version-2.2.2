"use client"

import { useEffect, useState } from "react"
import { useRouter } from "next/navigation"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { 
  Home, 
  CalendarCheck, 
  Heart, 
  Bell, 
  User, 
  Search,
  Star,
  Wallet,
  CheckCircle
} from "lucide-react"
import Link from "next/link"

export default function CustomerDashboard() {
  const router = useRouter()
  const [user, setUser] = useState<any>(null)
  const [stats, setStats] = useState({
    activeBookings: 0,
    completedServices: 0,
    averageRating: 0,
    totalSpent: 0
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
      id: 2,
      name: "Customer User",
      email: "customer@jobsmtaani.com",
      role: "customer",
      avatar: "/placeholder-user.jpg"
    })

    // Mock stats data
    setStats({
      activeBookings: 5,
      completedServices: 28,
      averageRating: 4.8,
      totalSpent: 18750
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
          <p className="text-primary-foreground/80 text-sm">Customer Dashboard</p>
        </div>
        
        <nav className="space-y-2">
          <Link href="/customer" className="flex items-center gap-3 p-3 rounded-lg bg-white/10">
            <Home className="h-5 w-5" />
            <span>Dashboard</span>
          </Link>
          <Link href="/customer/bookings" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
            <CalendarCheck className="h-5 w-5" />
            <span>My Bookings</span>
          </Link>
          <Link href="/customer/favorites" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
            <Heart className="h-5 w-5" />
            <span>Favorites</span>
          </Link>
          <Link href="/customer/notifications" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
            <Bell className="h-5 w-5" />
            <span>Notifications</span>
          </Link>
          <Link href="/customer/profile" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
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
          <h2 className="text-2xl font-bold">Customer Dashboard</h2>
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
                <p className="text-xs text-muted-foreground">{user.role}</p>
              </div>
            </div>
          </div>
        </header>

        {/* Welcome Section */}
        <main className="flex-1 p-6 bg-gray-50">
          <div className="mb-8">
            <h1 className="text-3xl font-bold">Welcome back, {user.name.split(" ")[0]}!</h1>
            <p className="text-muted-foreground">Find and book trusted local services</p>
          </div>

          {/* Stats Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Active Bookings</CardTitle>
                <CalendarCheck className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{stats.activeBookings}</div>
                <p className="text-xs text-muted-foreground">Services in progress</p>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Completed Services</CardTitle>
                <CheckCircle className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{stats.completedServices}</div>
                <p className="text-xs text-muted-foreground">Successfully completed</p>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Average Rating</CardTitle>
                <Star className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{stats.averageRating}</div>
                <p className="text-xs text-muted-foreground">Your service ratings</p>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Total Spent</CardTitle>
                <Wallet className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">KES {stats.totalSpent.toLocaleString()}</div>
                <p className="text-xs text-muted-foreground">On services</p>
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
                    { id: 1, service: "Plumbing Repair", provider: "John Mwangi", date: "Oct 5, 2025", status: "Confirmed", amount: 3500 },
                    { id: 2, service: "House Cleaning", provider: "Sarah Johnson", date: "Oct 3, 2025", status: "Completed", amount: 2800 },
                    { id: 3, service: "Electrical Work", provider: "Michael Ochieng", date: "Oct 1, 2025", status: "In Progress", amount: 4200 },
                  ].map((booking) => (
                    <div key={booking.id} className="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                      <div>
                        <p className="font-medium">{booking.service}</p>
                        <p className="text-sm text-muted-foreground">Provider: {booking.provider}</p>
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
                  <Link href="/customer/bookings">View All Bookings</Link>
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
                    <Link href="/services">
                      <Search className="h-4 w-4 mr-2" />
                      Find Services
                    </Link>
                  </Button>
                  <Button asChild variant="outline" className="w-full justify-start">
                    <Link href="/customer/bookings">
                      <CalendarCheck className="h-4 w-4 mr-2" />
                      View Bookings
                    </Link>
                  </Button>
                  <Button asChild variant="outline" className="w-full justify-start">
                    <Link href="/customer/favorites">
                      <Heart className="h-4 w-4 mr-2" />
                      My Favorites
                    </Link>
                  </Button>
                  <Button asChild variant="outline" className="w-full justify-start">
                    <Link href="/customer/profile">
                      <User className="h-4 w-4 mr-2" />
                      Edit Profile
                    </Link>
                  </Button>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle>Top Categories</CardTitle>
                  <CardDescription>Most booked services</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-3">
                    {[
                      { category: "Home Services", bookings: 12 },
                      { category: "Beauty & Wellness", bookings: 8 },
                      { category: "Automotive", bookings: 5 },
                    ].map((item, index) => (
                      <div key={index} className="flex items-center justify-between">
                        <span>{item.category}</span>
                        <Badge variant="secondary">{item.bookings} bookings</Badge>
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