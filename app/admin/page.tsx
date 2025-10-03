"use client"

import { useEffect, useState } from "react"
import { useRouter } from "next/navigation"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { 
  Users, 
  ConciergeBell, 
  CalendarCheck, 
  Wallet, 
  Star, 
  Settings, 
  BarChart3, 
  Bell, 
  Tags,
  CreditCard,
  Shield
} from "lucide-react"
import Link from "next/link"

export default function AdminDashboard() {
  const router = useRouter()
  const [user, setUser] = useState<any>(null)
  const [stats, setStats] = useState({
    totalUsers: 0,
    totalServices: 0,
    totalBookings: 0,
    pendingBookings: 0,
    totalRevenue: 0
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
      id: 1,
      name: "Admin User",
      email: "admin@jobsmtaani.com",
      role: "superadmin",
      avatar: "/placeholder-user.jpg"
    })

    // Mock stats data
    setStats({
      totalUsers: 1242,
      totalServices: 356,
      totalBookings: 1876,
      pendingBookings: 24,
      totalRevenue: 245670
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
          <p className="text-primary-foreground/80 text-sm">Admin Dashboard</p>
        </div>
        
        <nav className="space-y-2">
          <Link href="/admin" className="flex items-center gap-3 p-3 rounded-lg bg-white/10">
            <BarChart3 className="h-5 w-5" />
            <span>Dashboard</span>
          </Link>
          <Link href="/admin/users" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
            <Users className="h-5 w-5" />
            <span>Users Management</span>
          </Link>
          <Link href="/admin/services" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
            <ConciergeBell className="h-5 w-5" />
            <span>Services</span>
          </Link>
          <Link href="/admin/bookings" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
            <CalendarCheck className="h-5 w-5" />
            <span>Bookings</span>
          </Link>
          <Link href="/admin/categories" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
            <Tags className="h-5 w-5" />
            <span>Categories</span>
          </Link>
          <Link href="/admin/payments" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
            <CreditCard className="h-5 w-5" />
            <span>Payments</span>
          </Link>
          <Link href="/admin/reviews" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
            <Star className="h-5 w-5" />
            <span>Reviews</span>
          </Link>
          <Link href="/admin/settings" className="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
            <Settings className="h-5 w-5" />
            <span>Settings</span>
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
          <h2 className="text-2xl font-bold">Dashboard Overview</h2>
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

        {/* Stats Grid */}
        <main className="flex-1 p-6 bg-gray-50">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Total Users</CardTitle>
                <Users className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{stats.totalUsers.toLocaleString()}</div>
                <p className="text-xs text-muted-foreground">+12% from last month</p>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Active Services</CardTitle>
                <ConciergeBell className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{stats.totalServices.toLocaleString()}</div>
                <p className="text-xs text-muted-foreground">+8% from last month</p>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Total Bookings</CardTitle>
                <CalendarCheck className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{stats.totalBookings.toLocaleString()}</div>
                <p className="text-xs text-muted-foreground">+19% from last month</p>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Total Revenue</CardTitle>
                <Wallet className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">KES {stats.totalRevenue.toLocaleString()}</div>
                <p className="text-xs text-muted-foreground">+23% from last month</p>
              </CardContent>
            </Card>
          </div>

          {/* Recent Activity */}
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <Card className="lg:col-span-2">
              <CardHeader>
                <CardTitle>Recent Activity</CardTitle>
                <CardDescription>Latest platform activities</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {[
                    { id: 1, activity: "New service created", user: "John Mwangi", type: "Provider", time: "2 hours ago" },
                    { id: 2, activity: "Booking confirmed", user: "Sarah Johnson", type: "Customer", time: "4 hours ago" },
                    { id: 3, activity: "New user registered", user: "Michael Ochieng", type: "Provider", time: "1 day ago" },
                    { id: 4, activity: "Payment processed", user: "Grace Wanjiku", type: "Customer", time: "1 day ago" },
                  ].map((item) => (
                    <div key={item.id} className="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                      <div>
                        <p className="font-medium">{item.activity}</p>
                        <p className="text-sm text-muted-foreground">{item.user} • {item.type}</p>
                      </div>
                      <Badge variant="secondary">{item.time}</Badge>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>

            <div className="space-y-6">
              <Card>
                <CardHeader>
                  <CardTitle>Pending Actions</CardTitle>
                  <CardDescription>Items requiring attention</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="flex items-center justify-between p-3 border rounded-lg">
                    <div>
                      <p className="font-medium">Pending Bookings</p>
                      <p className="text-sm text-muted-foreground">Bookings awaiting confirmation</p>
                    </div>
                    <Badge>{stats.pendingBookings}</Badge>
                  </div>
                  <div className="flex items-center justify-between p-3 border rounded-lg">
                    <div>
                      <p className="font-medium">Support Tickets</p>
                      <p className="text-sm text-muted-foreground">Open support requests</p>
                    </div>
                    <Badge variant="destructive">3</Badge>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle>Quick Actions</CardTitle>
                  <CardDescription>Common administrative tasks</CardDescription>
                </CardHeader>
                <CardContent className="grid grid-cols-2 gap-3">
                  <Button asChild variant="outline" className="h-16 flex flex-col gap-1">
                    <Link href="/admin/users">
                      <Users className="h-5 w-5" />
                      <span>Manage Users</span>
                    </Link>
                  </Button>
                  <Button asChild variant="outline" className="h-16 flex flex-col gap-1">
                    <Link href="/admin/services">
                      <ConciergeBell className="h-5 w-5" />
                      <span>Manage Services</span>
                    </Link>
                  </Button>
                  <Button asChild variant="outline" className="h-16 flex flex-col gap-1">
                    <Link href="/admin/bookings">
                      <CalendarCheck className="h-5 w-5" />
                      <span>View Bookings</span>
                    </Link>
                  </Button>
                  <Button asChild variant="outline" className="h-16 flex flex-col gap-1">
                    <Link href="/admin/settings">
                      <Settings className="h-5 w-5" />
                      <span>Settings</span>
                    </Link>
                  </Button>
                </CardContent>
              </Card>
            </div>
          </div>
        </main>
      </div>
    </div>
  )
}