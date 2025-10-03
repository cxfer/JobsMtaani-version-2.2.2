"use client"

import { createContext, useContext, useState, useEffect, ReactNode } from 'react'
import { useRouter } from 'next/navigation'

interface User {
  id: number
  name: string
  email: string
  role: string
  avatar?: string
}

interface AuthContextType {
  user: User | null
  login: (email: string, password: string) => Promise<boolean>
  logout: () => void
  isLoading: boolean
}

const AuthContext = createContext<AuthContextType | undefined>(undefined)

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const router = useRouter()

  useEffect(() => {
    // Check if user is already logged in
    const token = localStorage.getItem('authToken')
    if (token) {
      // In a real app, you would verify the token with your backend
      // For now, we'll mock a user object
      const mockUser: User = {
        id: 1,
        name: "Demo User",
        email: "demo@jobsmtaani.com",
        role: "customer", // This would come from the token in a real app
        avatar: "/placeholder-user.jpg"
      }
      setUser(mockUser)
    }
    setIsLoading(false)
  }, [])

  const login = async (email: string, password: string): Promise<boolean> => {
    // In a real app, you would make an API call to authenticate the user
    // For now, we'll mock the authentication
    
    // Simulate API call delay
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Mock authentication logic
    if (email && password) {
      // Store token in localStorage (in a real app, this would be a JWT)
      localStorage.setItem('authToken', 'mock-jwt-token')
      
      // Set user data
      const mockUser: User = {
        id: 1,
        name: "Demo User",
        email: email,
        role: email.includes('admin') ? 'superadmin' : 
              email.includes('provider') ? 'service_provider' : 'customer',
        avatar: "/placeholder-user.jpg"
      }
      setUser(mockUser)
      
      // Redirect based on role
      if (mockUser.role === 'superadmin') {
        router.push('/admin')
      } else if (mockUser.role === 'service_provider') {
        router.push('/provider')
      } else {
        router.push('/customer')
      }
      
      return true
    }
    
    return false
  }

  const logout = () => {
    localStorage.removeItem('authToken')
    setUser(null)
    router.push('/login')
  }

  return (
    <AuthContext.Provider value={{ user, login, logout, isLoading }}>
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  const context = useContext(AuthContext)
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider')
  }
  return context
}