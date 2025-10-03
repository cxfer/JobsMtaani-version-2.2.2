import { NextResponse } from 'next/server'

// Mock authentication endpoint
export async function POST(request: Request) {
  const { email, password } = await request.json()
  
  // Mock authentication logic
  if (email && password) {
    // Determine user role based on email
    let role = 'customer'
    if (email.includes('admin')) {
      role = 'superadmin'
    } else if (email.includes('provider')) {
      role = 'service_provider'
    }
    
    // Return mock token and user data
    return NextResponse.json({
      token: 'mock-jwt-token',
      user: {
        id: 1,
        name: "Demo User",
        email,
        role
      }
    })
  }
  
  return NextResponse.json(
    { error: 'Invalid credentials' },
    { status: 401 }
  )
}