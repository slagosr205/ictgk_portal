import React from 'react'
import { useBranding } from './BrandingContext'

export default function BrandingExample() {
  const branding = useBranding()

  const logo = branding?.assets?.logo_url
  const bg = branding?.assets?.background_url
  const name = branding?.name

  return (
    <div
      className="p-6 rounded-xl text-white"
      style={{
        backgroundImage: bg ? `url(${bg})` : undefined,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        backgroundColor: 'var(--brand-dark)',
      }}
    >
      <div className="flex items-center gap-3">
        {logo ? <img src={logo} alt={name ?? 'Logo'} style={{ height: 40 }} /> : null}
        <div>
          <div className="text-lg font-semibold">{name}</div>
          <div className="text-sm" style={{ color: 'rgba(255,255,255,0.85)' }}>
            primary:{' '}
            <span style={{ color: 'var(--brand-accent)' }}>{branding?.palette?.primary}</span>
          </div>
        </div>
      </div>

      <div className="mt-4 flex gap-2">
        <button className="px-4 py-2 rounded-md" style={{ background: 'var(--brand-primary)', color: 'white' }}>
          Boton primario
        </button>
        <button
          className="px-4 py-2 rounded-md border"
          style={{ borderColor: 'var(--brand-accent)', color: 'var(--brand-accent)' }}
        >
          Boton secundario
        </button>
      </div>
    </div>
  )
}
