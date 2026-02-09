import './bootstrap'

// Auto-load all JS modules
import.meta.glob([
  './components/**/*.js',
  './pages/**/*.js',
], { eager: true })
