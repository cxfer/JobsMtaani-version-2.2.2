"use client"

import type React from "react"

import { useState, useEffect } from "react"
import Link from "next/link"
import { Search, Shield, Clock, Star, ChevronRight } from "lucide-react"
import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Badge } from "@/components/ui/badge"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"

interface Service {
  id: number
  title: string
  description: string
  price: number
  provider_name: string
  category: string
  rating: number
  image_url?: string
}

interface Testimonial {
  id: number
  name: string
  location: string
  rating: number
  comment: string
  avatar?: string
}

export default function HomePage() {
  const [popularServices, setPopularServices] = useState<Service[]>([])
  const [loading, setLoading] = useState(true)
  const [searchQuery, setSearchQuery] = useState("")
  const [selectedCategory, setSelectedCategory] = useState("all")
  const [location, setLocation] = useState("")

  useEffect(() => {
    // Load popular services
    loadPopularServices()
  }, [])

  const loadPopularServices = async () => {
    try {
      // In a real app, this would fetch from your API
      // For now, we'll use mock data
      const mockServices: Service[] = [
        {
          id: 1,
          title: "Professional House Cleaning",
          description: "Deep cleaning service for your home",
          price: 2500,
          provider_name: "Clean Masters",
          category: "Home Services",
          rating: 4.8,
          image_url: "/house-cleaning.png",
        },
        {
          id: 2,
          title: "Plumbing Repair Services",
          description: "Expert plumbing solutions for all your needs",
          price: 1500,
          provider_name: "Fix It Pro",
          category: "Home Services",
          rating: 4.9,
          image_url: "/plumbing-repair.jpg",
        },
        {
          id: 3,
          title: "Hair Styling & Makeup",
          description: "Professional beauty services at your location",
          price: 3000,
          provider_name: "Beauty Experts",
          category: "Beauty & Wellness",
          rating: 4.7,
          image_url: "/hair-styling-makeup.jpg",
        },
        {
          id: 4,
          title: "Car Wash & Detailing",
          description: "Complete car cleaning and detailing service",
          price: 2000,
          provider_name: "Auto Care",
          category: "Automotive",
          rating: 4.6,
          image_url: "/car-wash-detailing.jpg",
        },
        {
          id: 5,
          title: "Event Photography",
          description: "Capture your special moments professionally",
          price: 15000,
          provider_name: "Lens Masters",
          category: "Events",
          rating: 4.9,
          image_url: "/event-photography.png",
        },
        {
          id: 6,
          title: "Math Tutoring",
          description: "One-on-one math tutoring for all levels",
          price: 1000,
          provider_name: "EduExperts",
          category: "Tutoring",
          rating: 4.8,
          image_url: "/math-tutoring.png",
        },
      ]

      setPopularServices(mockServices)
      setLoading(false)
    } catch (error) {
      console.error("Error loading services:", error)
      setLoading(false)
    }
  }

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault()
    // Redirect to services page with search parameters
    const params = new URLSearchParams()
    if (searchQuery) params.set("search", searchQuery)
    if (selectedCategory !== "all") params.set("category", selectedCategory)
    if (location) params.set("location", location)

    window.location.href = `/services?${params.toString()}`
  }

  const testimonials: Testimonial[] = [
    {
      id: 1,
      name: "Sarah Johnson",
      location: "Nairobi",
      rating: 5,
      comment:
        "Amazing platform! Found a reliable plumber within minutes. The booking process was so smooth and the service was excellent.",
      avatar: "/woman-profile.png",
    },
    {
      id: 2,
      name: "Michael Ochieng",
      location: "Electrician",
      rating: 5,
      comment:
        "As a service provider, this platform has helped me grow my business significantly. Great customer support and easy to use.",
      avatar: "/man-profile.png",
    },
    {
      id: 3,
      name: "Grace Wanjiku",
      location: "Mombasa",
      rating: 5,
      comment:
        "Love how I can track my bookings and payments all in one place. The mobile app makes it even more convenient.",
      avatar: "/woman-profile.png",
    },
  ]

  return (
    <main className="min-h-screen">
      {/* Hero Section */}
      <section className="relative py-20 bg-gradient-to-br from-green-700 to-lime-500 text-white overflow-hidden">
        <div className="container mx-auto px-4">
          <div className="grid lg:grid-cols-2 gap-12 items-center min-h-[500px]">
            <div className="space-y-6">
              <h1 className="text-4xl lg:text-6xl font-bold text-balance leading-tight">
                Find Local Services Near You
              </h1>
              <p className="text-xl text-green-50 text-pretty max-w-lg">
                Connect with trusted service providers in your area. From home repairs to personal services, we've got
                you covered.
              </p>
              <div className="flex flex-col sm:flex-row gap-4">
                <Button asChild size="lg" className="bg-white text-green-700 hover:bg-green-50 px-8 py-6 text-lg">
                  <Link href="/services">Browse Services</Link>
                </Button>
                <Button
                  asChild
                  variant="outline"
                  size="lg"
                  className="border-white text-white hover:bg-white hover:text-green-700 px-8 py-6 text-lg bg-transparent"
                >
                  <Link href="/register">Join as Provider</Link>
                </Button>
              </div>
            </div>
            <div className="relative">
              <img
                src="/diverse-group-of-service-providers.jpg"
                alt="Service Platform"
                className="w-full h-auto rounded-lg shadow-2xl"
              />
            </div>
          </div>
        </div>
      </section>

      {/* Search Section */}
      <section className="py-8 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="max-w-4xl mx-auto">
            <Card className="shadow-lg border-0">
              <CardContent className="p-6">
                <form onSubmit={handleSearch} className="grid grid-cols-1 md:grid-cols-12 gap-4">
                  <div className="md:col-span-4">
                    <Input
                      type="text"
                      placeholder="What service do you need?"
                      value={searchQuery}
                      onChange={(e) => setSearchQuery(e.target.value)}
                      className="h-12 text-lg"
                    />
                  </div>
                  <div className="md:col-span-3">
                    <Select value={selectedCategory} onValueChange={setSelectedCategory}>
                      <SelectTrigger className="h-12 text-lg">
                        <SelectValue placeholder="All Categories" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="all">All Categories</SelectItem>
                        <SelectItem value="home">Home Services</SelectItem>
                        <SelectItem value="beauty">Beauty & Wellness</SelectItem>
                        <SelectItem value="automotive">Automotive</SelectItem>
                        <SelectItem value="events">Events</SelectItem>
                        <SelectItem value="tutoring">Tutoring</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <div className="md:col-span-3">
                    <Input
                      type="text"
                      placeholder="Your location"
                      value={location}
                      onChange={(e) => setLocation(e.target.value)}
                      className="h-12 text-lg"
                    />
                  </div>
                  <div className="md:col-span-2">
                    <Button type="submit" className="w-full h-12 text-lg">
                      <Search className="w-5 h-5 mr-2" />
                      Search
                    </Button>
                  </div>
                </form>
              </CardContent>
            </Card>
          </div>
        </div>
      </section>

      {/* Popular Services Section */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl lg:text-5xl font-bold text-balance mb-4">Popular Services</h2>
            <p className="text-xl text-muted-foreground">Most requested services in your area</p>
          </div>

          {loading ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {[...Array(6)].map((_, i) => (
                <Card key={i} className="h-80 animate-pulse">
                  <div className="h-48 bg-gray-200 rounded-t-lg"></div>
                  <CardContent className="p-4 space-y-2">
                    <div className="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div className="h-3 bg-gray-200 rounded w-1/2"></div>
                    <div className="h-3 bg-gray-200 rounded w-1/4"></div>
                  </CardContent>
                </Card>
              ))}
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {popularServices.map((service) => (
                <Card key={service.id} className="group hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                  <div className="relative h-48 overflow-hidden">
                    <img
                      src={service.image_url || "/placeholder.svg"}
                      alt={service.title}
                      className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                    <Badge className="absolute top-3 right-3 bg-white text-gray-900">{service.category}</Badge>
                  </div>
                  <CardContent className="p-4">
                    <h3 className="font-bold text-lg mb-2 group-hover:text-primary transition-colors">
                      {service.title}
                    </h3>
                    <p className="text-muted-foreground text-sm mb-3 line-clamp-2">{service.description}</p>
                    <div className="flex items-center justify-between mb-3">
                      <span className="text-sm text-muted-foreground">by {service.provider_name}</span>
                      <div className="flex items-center gap-1">
                        <Star className="w-4 h-4 fill-yellow-400 text-yellow-400" />
                        <span className="text-sm font-medium">{service.rating}</span>
                      </div>
                    </div>
                    <div className="flex items-center justify-between">
                      <span className="text-lg font-bold text-primary">KES {service.price.toLocaleString()}</span>
                      <Button size="sm" className="group-hover:bg-primary group-hover:text-white">
                        Book Now
                        <ChevronRight className="w-4 h-4 ml-1" />
                      </Button>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          )}

          <div className="text-center mt-12">
            <Button asChild variant="outline" size="lg">
              <Link href="/services">View All Services</Link>
            </Button>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-16 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl lg:text-5xl font-bold text-balance mb-4">Why Choose JobsMtaani?</h2>
            <p className="text-xl text-muted-foreground">Your trusted platform for local services</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <Card className="text-center border-0 shadow-sm hover:shadow-md transition-shadow">
              <CardContent className="p-8">
                <div className="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                  <Shield className="w-10 h-10 text-primary" />
                </div>
                <h3 className="text-xl font-bold mb-4">Verified Providers</h3>
                <p className="text-muted-foreground">
                  All service providers are verified and rated by our community for your peace of mind.
                </p>
              </CardContent>
            </Card>

            <Card className="text-center border-0 shadow-sm hover:shadow-md transition-shadow">
              <CardContent className="p-8">
                <div className="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                  <Clock className="w-10 h-10 text-primary" />
                </div>
                <h3 className="text-xl font-bold mb-4">Quick Booking</h3>
                <p className="text-muted-foreground">
                  Book services instantly with our easy-to-use platform. Get confirmed bookings in minutes.
                </p>
              </CardContent>
            </Card>

            <Card className="text-center border-0 shadow-sm hover:shadow-md transition-shadow">
              <CardContent className="p-8">
                <div className="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                  <Star className="w-10 h-10 text-primary" />
                </div>
                <h3 className="text-xl font-bold mb-4">Quality Assured</h3>
                <p className="text-muted-foreground">
                  Rate and review services to help others make informed decisions and maintain quality standards.
                </p>
              </CardContent>
            </Card>
          </div>
        </div>
      </section>

      {/* Testimonials Section */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl lg:text-5xl font-bold text-balance mb-4">What Our Customers Say</h2>
            <p className="text-xl text-muted-foreground">Real reviews from satisfied customers</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {testimonials.map((testimonial) => (
              <Card key={testimonial.id} className="border-0 shadow-sm hover:shadow-md transition-shadow">
                <CardContent className="p-6">
                  <div className="flex mb-4">
                    {[...Array(testimonial.rating)].map((_, i) => (
                      <Star key={i} className="w-5 h-5 fill-yellow-400 text-yellow-400" />
                    ))}
                  </div>
                  <p className="text-muted-foreground mb-6 text-pretty">"{testimonial.comment}"</p>
                  <div className="flex items-center gap-3">
                    <Avatar>
                      <AvatarImage src={testimonial.avatar || "/placeholder.svg"} alt={testimonial.name} />
                      <AvatarFallback>
                        {testimonial.name
                          .split(" ")
                          .map((n) => n[0])
                          .join("")}
                      </AvatarFallback>
                    </Avatar>
                    <div>
                      <h4 className="font-semibold">{testimonial.name}</h4>
                      <p className="text-sm text-muted-foreground">{testimonial.location}</p>
                    </div>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </section>
    </main>
  )
}
