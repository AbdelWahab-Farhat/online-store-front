export interface AuthUser {
  id: number
  name: string
  email: string
  phone: string | null
  role: string | null
  role_label: string | null
  created_at: string | null
}

export interface LoginPayload {
  email: string
  password: string
}

export interface LoginResponse {
  message: string
  user: AuthUser
  token: string
}

export interface ProfileResponse {
  user: AuthUser
}
