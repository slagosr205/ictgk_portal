import React, { createContext, useContext, useEffect, useMemo, useState } from 'react'

const BrandingContext = createContext(null)

function readBrandingFromWindow() {
  if (typeof window === 'undefined') return null
  return window.__APP_BRANDING__ ?? null
}

function applyCssVars(cssVars) {
  if (typeof document === 'undefined') return
  const el = document.documentElement
  for (const [k, v] of Object.entries(cssVars || {})) {
    if (v == null) continue
    el.style.setProperty(k, String(v))
  }
}

export function BrandingProvider({ children, initialBranding }) {
  const [branding] = useState(() => initialBranding ?? readBrandingFromWindow() ?? {})

  useEffect(() => {
    applyCssVars(branding?.cssVars)
  }, [branding])

  const value = useMemo(() => branding, [branding])
  return <BrandingContext.Provider value={value}>{children}</BrandingContext.Provider>
}

export function useBranding() {
  const ctx = useContext(BrandingContext)
  if (!ctx) throw new Error('useBranding must be used within <BrandingProvider>')
  return ctx
}
