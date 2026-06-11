<template>
  <div class="property-page">
    <!-- Навигация -->
    <nav class="topnav">
      <span class="nav-brand" @click="goToHome">Sāpņu Māja</span>
      <ul class="nav-links">
        <li><button @click="goToHome">Sākums</button></li>
        <li><button @click="goToCatalog">Kategorijas</button></li>
        <li><button @click="goToAbout">Par mums</button></li>
        <li v-if="isRegistered && !isAdmin"><button @click="goToFavorites">FAVORĪTI</button></li>
      </ul>
      <div class="nav-right" v-if="isRegistered">
        <span class="user-name">{{ userName }}</span>
        <button class="logout-btn" @click="Logout">IZIET</button>
      </div>
    </nav>

    <div class="property-container" v-if="property">
      <button class="back-btn" @click="goBack">
        <span class="back-arr">←</span> Atpakaļ
      </button>

      <div v-if="isAdmin" class="admin-delete-bar">
          <button class="admin-edit-btn" @click="goToEdit">REDIĢĒT</button>
        <button class="admin-delete-btn" @click="deleteProperty">DZĒST ĪPAŠUMU</button>
      </div>

      <!-- ВЕРХНЯЯ ЧАСТЬ: Фотографии -->
      <div class="property-gallery-section">
        <!-- Главное фото -->
        <div class="main-image">
          <img :src="mainImage" :alt="property.adrese" />
        </div>
        <!-- Миниатюры (3 штуки в ряд) -->
        <div class="thumbnail-row">
          <div 
            v-for="(image, index) in galleryImages" 
            :key="index"
            class="thumbnail"
            :class="{ active: currentIndex === index }"
            @click="setCurrentImage(index)"
          >
            <img :src="image" :alt="property.adrese" />
          </div>
        </div>
      </div>

      <!-- НИЖНЯЯ ЧАСТЬ: Две колонки -->
      <div class="property-bottom">
        <!-- ЛЕВАЯ КОЛОНКА: Информация, описание и кнопка -->
        <div class="property-info-left">
          <div class="property-header">
            <span class="property-badge">{{ getTypeLabel(property.cat_slogan) }}</span>
            <span class="property-location">{{ property.pilseta }}, {{ property.rajons || 'Latvija' }}</span>
            <h1 class="property-title">{{ property.virsraksts || property.adrese }}</h1>
            <p class="property-address">{{ property.adrese }}</p>
          </div>

          <div class="property-description">
            <p>{{ property.apraksts }}</p>
          </div>

          
        </div>

        <!-- ПРАВАЯ КОЛОНКА: Цена и характеристики -->
        <div class="property-details-right">
          <!-- Цена -->
          <div class="price-card">
            <div class="price-value">{{ formatPrice(property.cena) }} <span>EUR</span></div>
            <div class="price-label">CENA</div>
          </div>

          <!-- Характеристики -->
          <div class="features-grid">
            <div class="feature-item" v-if="property.platiba">
              <span class="feature-label">PLATĪBA</span>
              <span class="feature-value">{{ property.platiba }} m²</span>
            </div>
            <div class="feature-item" v-if="property.istabu_skaits">
              <span class="feature-label">ISTABAS</span>
              <span class="feature-value">{{ property.istabu_skaits }}</span>
            </div>
            <div class="feature-item" v-if="property.stavu_skaits">
              <span class="feature-label">STĀVI / STĀVS</span>
              <span class="feature-value">{{ property.stavu_skaits }}</span>
            </div>
            <div class="feature-item" v-if="property.zemes_platiba">
              <span class="feature-label">ZEMES PLATĪBA</span>
              <span class="feature-value">{{ property.zemes_platiba }} are</span>
            </div>
            <div class="feature-item" v-if="property.celsanas_gads">
              <span class="feature-label">CELŠANAS GADS</span>
              <span class="feature-value">{{ property.celsanas_gads }}</span>
            </div>
            <div class="feature-item" v-if="property.stavoklis">
              <span class="feature-label">STĀVOKLIS</span>
              <span class="feature-value">{{ property.stavoklis }}</span>
            </div>
          </div>

          <!-- Дополнительные свойства -->
          <div class="extra-properties" v-if="property.ipasibas">
            <div class="extra-title">ĪPAŠĪBAS</div>
            <div class="extra-list">
              <span v-for="(item, index) in property.ipasibas.split(',')" :key="index" class="extra-tag">
                {{ item.trim() }}
              </span>
            </div>
          </div>

          <button v-if="isRegistered && !isAdmin" class="favorite-btn" @click="toggleFavorite" :class="{ active: isFavorite }">
            <span class="heart-icon">{{ isFavorite ? '❤️' : '♡' }}</span>
            {{ isFavorite ? 'NOŅEMT NO IZLASES' : 'PIEVIENOT IZLASEI' }} →
          </button>
          
          <!-- Кнопка для гостей -->
          <button v-else-if="!isRegistered" class="contact-btn" @click="openAuthModal">
            PIERAKSTĪTIES, LAI PIEVIENOTU IZLASEI →
          </button>

         
        </div>
      </div>
    </div>

    <div v-else class="loading">
      <p>Ielādē...</p>
    </div>
    
    <!-- Модальное окно для входа/регистрации -->
    <div v-if="showAuthModal" class="modal-overlay" @click="closeModal">
      <div class="auth-modal" @click.stop>
        <div class="auth-tabs">
          <button 
            class="auth-tab" 
            :class="{ active: activeTab === 'login' }"
            @click="activeTab = 'login'"
          >
            IEIET
          </button>
          <button 
            class="auth-tab" 
            :class="{ active: activeTab === 'register' }"
            @click="activeTab = 'register'"
          >
            REĢISTRĒTIES
          </button>
        </div>

        <!-- Форма входа -->
        <div v-if="activeTab === 'login'" class="auth-form">
          <h3 class="form-title">Laipni lūgti atpakaļ!</h3>
          <p class="form-subtitle">
            Piesakieties, lai rakstītu atsauksmes un sekotu līdzi interesantajiem īpašumiem.
          </p>

          <div class="form-group">
            <label class="form-label">E-PASTS</label>
            <input 
              type="email" 
              class="form-input" 
              placeholder="jusu@epasts.lv"
              v-model="loginForm.epasts"
            />
          </div>

          <div class="form-group">
            <label class="form-label">PAROLE</label>
            <input 
              :type="showLoginPassword ? 'text' : 'password'" 
              class="form-input" 
              placeholder="......"
              v-model="loginForm.parole"
            />
           
          </div>

          <button class="submit-btn" @click="Login">IEIET</button>
        </div>

        <!-- Форма регистрации -->
        <div v-else class="auth-form">
          <h3 class="form-title">Izveidot kontu</h3>
          <p class="form-subtitle">
            Reģistrējieties, lai pievienotu atsauksmes un saglabātu meklēšanas rezultātus.
          </p>

          <div class="form-group">
            <label class="form-label">VĀRDS</label>
            <input 
              type="text" 
              class="form-input" 
              placeholder="Jūsu vārds un uzvārds"
              v-model="registerForm.lietotajvards"
            />
          </div>

          <div class="form-group">
            <label class="form-label">E-PASTS</label>
            <input 
              type="email" 
              class="form-input" 
              placeholder="jusu@epasts.lv"
              v-model="registerForm.epasts"
            />
          </div>

          <div class="form-group">
            <label class="form-label">PAROLE</label>
            <input 
              :type="showRegisterPassword ? 'text' : 'password'" 
              class="form-input" 
              placeholder="Vismaz 6 rakstzīmes"
              v-model="registerForm.parole"
            />
            
          </div>

          
          <div v-if="registerError" class="error-message">
            {{ registerError }}
          </div>
          <div v-if="registerSuccess" class="success-message">
            {{ registerSuccess }}
          </div>

          <button class="submit-btn" @click="Register" :disabled="registerLoading">
            {{ registerLoading ? 'Lādē...' : 'REĢISTRĒTIES' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'PropertyDetail',
  data() {
    return {
      property: null,
      currentIndex: 0,
      showAuthModal: false,
      activeTab: 'login',
      showLoginPassword: false,
      showRegisterPassword: false,
      loginLoading: false,
      registerLoading: false,
      loginError: null,
      registerError: null,
      registerSuccess: null,
      isFavorite: false,
      loginForm: {
        epasts: '',
        parole: ''
      },
      registerForm: {
        lietotajvards: '',
        epasts: '',
        parole: '',
        loma: 'registrets'
      },
    }
  },
  
  computed: {
    mainImage() {
      // Проверяем, что property существует
      if (!this.property) return '/images/default-house.jpg'
      
      // Собираем все фото для поиска текущего
      const allPhotos = []
      
      if (this.property.attels) {
        allPhotos.push(this.property.attels)
      }
      
      if (this.property.atteli && this.property.atteli.length > 0) {
        this.property.atteli.forEach(img => {
          if (img.attels_fail) {
            allPhotos.push(img.attels_fail)
          }
        })
      }
      
      if (allPhotos.length > 0 && this.currentIndex < allPhotos.length) {
        return allPhotos[this.currentIndex]
      }
      return '/images/default-house.jpg'
    },
    
    galleryImages() {
      // Проверяем, что property существует
      if (!this.property) return []
      
      const images = []

      if (this.property.attels) {
        images.push(this.property.attels)
      }
  
      
      // Только дополнительные фото (миниатюры)
      if (this.property.atteli && this.property.atteli.length > 0) {
        this.property.atteli.forEach(img => {
          if (img.attels_fail) {
            images.push(img.attels_fail)
          }
        })
      }
      
      return images
    },

     isGuest() {
      const user = localStorage.getItem('user')
      if (!user) return true
      try {
        const userData = JSON.parse(user)
        return userData.loma === 'viesis'
      } catch(e) {
        return true
      }
    },

     isRegistered() {
      const user = localStorage.getItem('user')
      if (!user) return false
      try {
        const userData = JSON.parse(user)
        return userData.loma === 'registrets' || userData.loma === 'admins'
      } catch(e) {
        return false
      }
    },

    isAdmin() {
      const user = localStorage.getItem('user')
      if (!user) return false
      try {
        const userData = JSON.parse(user)
        return userData.loma === 'admins'
      } catch(e) {
        return false
      }
    },

    userName() {
      const user = localStorage.getItem('user')
      if (user) {
        try {
          const userData = JSON.parse(user)
          return userData.lietotajvards
        } catch(e) {
          return ''
        }
      }
      return ''
    }
  },
  
  mounted() {
    

    if (this.$route.params.id === '0') {
      this.openAuthModal()
    } else {
      this.fetchProperty()
    }
    this.checkFavoriteStatus()
  

    console.log('User in localStorage:', localStorage.getItem('user'))
    console.log('Is registered:', this.isRegistered)
  },

  
  
  methods: {

    goToEdit() {
      // Сохраняем данные для редактирования
      sessionStorage.setItem('edit_property_id', this.property.SludinajumsID)
      sessionStorage.setItem('edit_property_data', JSON.stringify(this.property))
      this.$router.push('/catalog')
    },

    fetchProperty() {
      const id = this.$route.params.id
      axios.get(`http://localhost:8000/api/sludinajumi/${id}`)
        .then(response => {
          this.property = response.data
          this.currentIndex = 0
          console.log('Property loaded:', this.property)

          if (this.isRegistered) {
            this.checkFavoriteStatus()
          }
        })
        .catch(error => {
          console.error('Error loading property:', error)
        })
    },
    
    setCurrentImage(index) {
      this.currentIndex = index
    },
    
    formatPrice(price) {
      if (!price) return '0'
      return Number(price).toLocaleString()
    },
    
    getTypeLabel(slogan) {
      const types = {
        'privatmaja': 'PRIVĀTMĀJA',
        'dzivoklis': 'DZĪVOKLIS',
        'lauki': 'LAUKU ĪPAŠUMS'
      }
      return types[slogan] || 'ĪPAŠUMS'
    },

    async checkFavoriteStatus() {
      if (!this.isRegistered || !this.property) return
      
      try {
        const token = localStorage.getItem('auth_token')
        const response = await axios.get(`http://localhost:8000/api/favorites/check/${this.property.SludinajumsID}`, {
          headers: { 'Authorization': `Bearer ${token}` }
        })
        this.isFavorite = response.data.is_favorite
      } catch (error) {
        console.error('Error checking favorite:', error)
      }
    },

    async toggleFavorite() {
      if (!this.isRegistered) {
        this.openAuthModal()
        return
      }
      
      try {
        const token = localStorage.getItem('auth_token')
        if (this.isFavorite) {
          await axios.delete(`http://localhost:8000/api/favorites/${this.property.SludinajumsID}`, {
            headers: { 'Authorization': `Bearer ${token}` }
          })
          this.isFavorite = false
          alert('Īpašums izņemts no izlases!')
        } else {
          await axios.post(`http://localhost:8000/api/favorites`, {
            sludinajums_id: this.property.SludinajumsID
          }, {
            headers: { 'Authorization': `Bearer ${token}` }
          })
          this.isFavorite = true
          alert('Īpašums pievienots izlasei!')
        }
      } catch (error) {
        console.error('Error toggling favorite:', error)
        alert('Kļūda! Lūdzu, mēģiniet vēlreiz.')
      }
    },

    async deleteProperty() {
      if (!confirm('Vai tiešām vēlaties dzēst šo īpašumu?')) return
      try {
        const token = localStorage.getItem('auth_token')
        await axios.delete(`http://localhost:8000/api/properties/${this.property.SludinajumsID}`, {
          headers: { 'Authorization': `Bearer ${token}` }
        })
        alert('Īpašums veiksmīgi dzēsts!')
        this.$router.push('/catalog')
      } catch (error) {
        console.error('Error deleting property:', error)
        alert('Kļūda dzēšot īpašumu!')
      }
    },


    openAuthModal() {
      this.showAuthModal = true
      this.activeTab = 'login'
      // Блокируем скролл на заднем фоне
      document.body.style.overflow = 'hidden'
    },
    
    closeModal() {
      this.showAuthModal = false
      // Возвращаем скролл
      document.body.style.overflow = ''
      // Очищаем формы
      this.loginForm = { email: '', password: '' }
      this.registerForm = { name: '', email: '', password: '' }
      this.loginError = null
      this.registerError = null
      this.registerSuccess = null
    },

    async Login() {
     this.loginLoading = true
      this.loginError = null
      
      try {
        const response = await axios.post('http://localhost:8000/api/login', {
          epasts: this.loginForm.epasts,
          parole: this.loginForm.parole
        })
        
        if (response.data.success) {
          // Сохраняем токен и данные пользователя
          localStorage.setItem('auth_token', response.data.token)
          localStorage.setItem('user', JSON.stringify(response.data.user))
          axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
          
          this.closeModal()
          alert('Veiksmīgi pierakstījies!')

          window.location.href = '/catalog'
        }
      } catch (error) {
        if (error.response) {
          this.loginError = error.response.data.message || 'Nepareizs e-pasts vai parole'
        } else {
          this.loginError = 'Kļūda savienojumā ar serveri'
        }
        console.error('Login error:', error)
      } finally {
        this.loginLoading = false
      }
    },
    
    
    async Register() {
      
      this.registerLoading = true
      this.registerError = null
      this.registerSuccess = null
      
      // Валидация
      if (!this.registerForm.parole || this.registerForm.parole.length < 6) {
        this.registerError = 'Parolei jābūt vismaz 6 rakstzīmēm'
        this.registerLoading = false
        return
      }

      if (!this.registerForm.loma) {
        this.registerError = 'Lūdzu, izvēlieties lomu!'
        return
      }
      
      if (!this.registerForm.lietotajvards || this.registerForm.lietotajvards.trim() === '') {
        this.registerError = 'Lūdzu, ievadiet lietotājvārdu'
        this.registerLoading = false
        return
      }
      
      if (!this.registerForm.epasts || this.registerForm.epasts.trim() === '') {
        this.registerError = 'Lūdzu, ievadiet e-pasta adresi'
        this.registerLoading = false
        return
      }
      
      try {
        const response = await axios.post('http://localhost:8000/api/register', {
          lietotajvards: this.registerForm.lietotajvards,
          epasts: this.registerForm.epasts,
          parole: this.registerForm.parole,
         
        })
        
        if (response.data.success) {
          this.registerSuccess = 'Reģistrācija veiksmīga! Tagad varat pierakstīties.'
          
          // Очищаем форму регистрации
          this.registerForm = {
            lietotajvards: '',
            epasts: '',
            parole: ''
          }
          
          // Автоматически переключаемся на форму входа через 2 секунды
          setTimeout(() => {
            this.activeTab = 'login'
            this.registerSuccess = null
          }, 2000)
        } else {
          this.registerError = response.data.message || 'Reģistrācija neizdevās'
        }
      } catch (error) {
        if (error.response) {
          this.registerError = error.response.data.message || 'Reģistrācija neizdevās'
        } else if (error.request) {
          this.registerError = 'Nav savienojuma ar serveri. Pārliecinieties, ka backend darbojas.'
        } else {
          this.registerError = 'Kļūda savienojumā ar serveri'
        }
        console.error('Register error:', error)
      } finally {
        this.registerLoading = false
      }

    },

    Logout() {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      delete axios.defaults.headers.common['Authorization']
      alert('Jūs esat izrakstījies')
      location.reload()
    },
    
    goToHome() {
      this.$router.push('/')
    },
    
    goToCatalog() {
      this.$router.push('/catalog')
    },
    
    goToAbout() {
      this.$router.push('/par-mums')
    },

    goToFavorites() {
      this.$router.push('/favorites')
    },
    
    goBack() {
      this.$router.back()
    }
  }
}
</script>

<style scoped>
/* ========== НАВИГАЦИЯ ========== */
.topnav {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 62px;
  background: rgba(240, 235, 226, 0.75);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(26, 26, 24, 0.09);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 64px;
  z-index: 1000;
}

.nav-brand {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 1.15rem;
  font-weight: 500;
  font-style: italic;
  color: #1a1a18;
  letter-spacing: 0.02em;
  cursor: pointer;
  opacity: 0.8;
  transition: opacity 0.3s ease;
}

.nav-brand:hover {
  opacity: 1;
}

.nav-links {
  display: flex;
  gap: 48px;
  list-style: none;
  margin: 0;
  padding: 0;
}

.nav-links li button {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.64rem;
  font-weight: 600;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: #1a1a18;
  background: none;
  border: none;
  cursor: pointer;
  padding-bottom: 5px;
  position: relative;
  opacity: 0.5;
  transition: opacity 0.25s ease;
}

.nav-links li button:hover {
  opacity: 0.8;
}

/* Правая часть навигации */
.nav-right {
  display: flex;
  align-items: center;
  gap: 20px;
}

.user-name {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.75rem;
  font-weight: 500;
  color: #1a1a18;
  opacity: 0.7;
}

.logout-btn {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.64rem;
  font-weight: 600;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  background: none;
  border: 1px solid rgba(26, 26, 24, 0.3);
  color: #1a1a18;
  padding: 6px 14px;
  cursor: pointer;
  transition: all 0.25s ease;
  border-radius: 4px;
}

.logout-btn:hover {
  background: #1a1a18;
  color: #f0ebe2;
  border-color: #1a1a18;
}

.admin-actions {
  display: flex;
  gap: 16px;
  justify-content: flex-end;
  margin-bottom: 24px;
}

.admin-edit-btn {
  background: #1a1a18;
  color: #f0ebe2;
  border: none;
  padding: 10px 24px;
  cursor: pointer;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  border-radius: 4px;
  transition: opacity 0.25s ease;
}

.admin-delete-btn {
  background: #c33;
  color: white;
  border: none;
  padding: 10px 24px;
  cursor: pointer;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  border-radius: 4px;
  transition: opacity 0.25s ease;
}

.admin-edit-btn:hover,
.admin-delete-btn:hover {
  opacity: 0.85;
}

/* ========== ОСНОВНЫЕ СТИЛИ ========== */
.property-page {
  width: 100%;
  min-height: 100vh;
  background: #f0ebe2;
  padding-top: 100px;
  padding-bottom: 80px;
}

.property-container {
  max-width: 1300px;
  margin: 0 auto;
  padding: 0 64px;
}

.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.62rem;
  font-weight: 600;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: rgba(26, 26, 24, 0.45);
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  margin-bottom: 40px;
  transition: color 0.25s ease;
}

.back-btn:hover {
  color: #1a1a18;
}

.back-arr {
  display: inline-block;
  transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
}

.back-btn:hover .back-arr {
  transform: translateX(-4px);
}

/* ========== ГАЛЕРЕЯ (верхняя часть) ========== */
.property-gallery-section {
  margin-bottom: 60px;
}

.main-image {
  width: 100%;
  max-width: 800px;
  margin: 0 auto;
  aspect-ratio: 16 / 9;
  overflow: hidden;
  background: #d9d2c5;
  margin-bottom: 16px;
}

.main-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.thumbnail-row {
  display: flex;
  justify-content: center;
  gap: 12px;
  max-width: 800px;
  margin: 0 auto;
}

.thumbnail {
  width: 120px;
  aspect-ratio: 1 / 1;
  overflow: hidden;
  cursor: pointer;
  opacity: 0.6;
  transition: opacity 0.25s ease;
  border: 2px solid transparent;
}

.thumbnail.active {
  opacity: 1;
  border-color: #1a1a18;
}

.thumbnail:hover {
  opacity: 1;
}

.thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* ========== НИЖНЯЯ ЧАСТЬ: Две колонки ========== */
.property-bottom {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  margin-top: 20px;
}

/* ЛЕВАЯ КОЛОНКА - Информация, описание и кнопка */
.property-info-left {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.property-header {
  border-bottom: 1px solid rgba(26, 26, 24, 0.1);
  padding-bottom: 16px;
}

.property-badge {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.65rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  color: rgba(26, 26, 24, 0.6);
  text-transform: uppercase;
  display: inline-block;
  margin-bottom: 8px;
}

.property-location {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
  font-weight: 500;
  letter-spacing: 0.08em;
  color: rgba(26, 26, 24, 0.5);
  text-transform: uppercase;
  display: block;
  margin-bottom: 8px;
}

.property-title {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 1.8rem;
  font-weight: 500;
  font-style: italic;
  color: #1a1a18;
  margin: 0 0 4px 0;
  line-height: 1.2;
}

.property-address {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.8rem;
  font-weight: 400;
  color: rgba(26, 26, 24, 0.6);
  margin: 0;
}

.property-description {
  font-family: Georgia, serif;
  font-size: 1rem;
  line-height: 1.6;
  color: rgba(26, 26, 24, 0.8);
}

.property-description p {
  margin: 0;
}

/* ПРАВАЯ КОЛОНКА - Цена и характеристики */
.property-details-right {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.price-card {
  background: white;
  padding: 24px;
  text-align: center;
  border: 1px solid rgba(26, 26, 24, 0.1);
}

.price-value {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 2rem;
  font-weight: 700;
  color: #1a1a18;
}

.price-value span {
  font-size: 0.8rem;
  font-weight: 500;
  margin-left: 4px;
  color: rgba(26, 26, 24, 0.5);
}

.price-label {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  color: rgba(26, 26, 24, 0.45);
  text-transform: uppercase;
  margin-top: 8px;
}

.features-grid {
  background: white;
  padding: 20px;
  border: 1px solid rgba(26, 26, 24, 0.1);
  display: grid;
  grid-template-columns: 1fr;
  gap: 14px;
}

.feature-item {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
}

.feature-label {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.65rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  color: rgba(26, 26, 24, 0.45);
  text-transform: uppercase;
}

.feature-value {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.9rem;
  font-weight: 500;
  color: #1a1a18;
}

.extra-properties {
  background: white;
  padding: 20px;
  border: 1px solid rgba(26, 26, 24, 0.1);
}

.extra-title {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  color: rgba(26, 26, 24, 0.6);
  text-transform: uppercase;
  margin-bottom: 12px;
}

.extra-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.extra-tag {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
  font-weight: 400;
  color: #1a1a18;
  background: rgba(26, 26, 24, 0.05);
  padding: 4px 12px;
}

/* Кнопка с сердечком */
.contact-btn {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  background: #1a1a18;
  color: #f0ebe2;
  border: none;
  padding: 14px 20px;
  cursor: pointer;
  transition: opacity 0.25s ease;
  width: 100%;
  margin-top: 16px;
}

.contact-btn:hover {
  opacity: 0.85;
}

.favorite-btn {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  background: #1a1a18;
  color: #f0ebe2;
  border: none;
  padding: 14px 20px;
  cursor: pointer;
  transition: all 0.25s ease;
  width: 100%;
  margin-top: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.favorite-btn:hover {
  opacity: 0.85;
}

.favorite-btn.active {
  background: #c33;
}

.favorite-btn.active:hover {
  background: #a22;
}

.heart-icon {
  font-size: 1.2rem;
}

/* ========== МОДАЛЬНОЕ ОКНО ========== */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.auth-modal {
  background: white;
  width: 90%;
  max-width: 500px;
  border-radius: 8px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    transform: translateY(30px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.auth-tabs {
  display: flex;
  border-bottom: 1px solid rgba(26, 26, 24, 0.1);
}

.auth-tab {
  flex: 1;
  background: none;
  border: none;
  padding: 18px 24px;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: rgba(26, 26, 24, 0.4);
  cursor: pointer;
  transition: all 0.25s ease;
  position: relative;
}

.auth-tab:hover {
  color: rgba(26, 26, 24, 0.7);
}

.auth-tab.active {
  color: #1a1a18;
}

.auth-tab.active::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 0;
  right: 0;
  height: 2px;
  background: #1a1a18;
}

.auth-form {
  padding: 40px 48px;
}

.form-title {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 1.5rem;
  font-weight: 500;
  font-style: italic;
  color: #1a1a18;
  margin: 0 0 12px 0;
  line-height: 1.2;
}

.form-subtitle {
  font-family: Georgia, serif;
  font-size: 0.85rem;
  line-height: 1.5;
  color: rgba(26, 26, 24, 0.6);
  margin: 0 0 32px 0;
}

.form-group {
  margin-bottom: 24px;
  position: relative;
}

.form-label {
  display: block;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.65rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  color: rgba(26, 26, 24, 0.5);
  text-transform: uppercase;
  margin-bottom: 8px;
}

.form-input {
  width: 100%;
  padding: 12px 0;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.9rem;
  color: #1a1a18;
  background: transparent;
  border: none;
  border-bottom: 1px solid rgba(26, 26, 24, 0.15);
  outline: none;
  transition: border-color 0.2s ease;
}

.form-input:focus {
  border-bottom-color: #1a1a18;
}

.form-input::placeholder {
  color: rgba(26, 26, 24, 0.3);
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.85rem;
}

.password-toggle {
  position: absolute;
  right: 0;
  bottom: 12px;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.1rem;
  padding: 0;
  color: rgba(26, 26, 24, 0.4);
  transition: color 0.2s ease;
}

.password-toggle:hover {
  color: rgba(26, 26, 24, 0.7);
}

.submit-btn {
  width: 100%;
  background: #1a1a18;
  color: #f0ebe2;
  border: none;
  padding: 14px 20px;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  cursor: pointer;
  transition: opacity 0.25s ease;
  margin-top: 16px;
}

.submit-btn:hover:not(:disabled) {
  opacity: 0.85;
}

.submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.error-message {
  background: #fee;
  color: #c33;
  padding: 10px;
  border-radius: 4px;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.75rem;
  margin-bottom: 16px;
}

.success-message {
  background: #efe;
  color: #3c3;
  padding: 10px;
  border-radius: 4px;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.75rem;
  margin-bottom: 16px;
}

/* Загрузка */
.loading {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.9rem;
  color: rgba(26, 26, 24, 0.55);
}

/* Адаптация */
@media (max-width: 960px) {
  .property-container {
    padding: 0 24px;
  }
  
  .property-bottom {
    grid-template-columns: 1fr;
    gap: 40px;
  }
  
  .thumbnail-row {
    justify-content: center;
  }
  
  .thumbnail {
    width: 100px;
  }
  
  .topnav {
    padding: 0 24px;
  }
  
  .nav-links {
    gap: 24px;
  }
  
  .auth-form {
    padding: 32px 32px;
  }
}

@media (max-width: 640px) {
  .property-title {
    font-size: 1.3rem;
  }
  
  .price-value {
    font-size: 1.5rem;
  }
  
  .thumbnail {
    width: 70px;
  }
  
  .auth-form {
    padding: 24px 24px;
  }
  
  .form-title {
    font-size: 1.3rem;
  }
  
  .nav-right {
    gap: 10px;
  }
  
  .user-name {
    font-size: 0.65rem;
  }
  
  .logout-btn {
    font-size: 0.55rem;
    padding: 4px 10px;
  }
}
</style>