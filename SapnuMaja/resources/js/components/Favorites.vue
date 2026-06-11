<template>
  <div class="favorites-page">
    <nav class="topnav">
      <span class="nav-brand" @click="goToHome">Sāpņu Māja</span>
      <ul class="nav-links">
        <li><button @click="goToHome">Sākums</button></li>
        <li><button @click="goToCatalog">Kategorijas</button></li>
        <li><button @click="goToAbout">Par mums</button></li>
        <li><button class="active">FAVORĪTI</button></li>
      </ul>
      <div class="nav-right">
        <span class="user-name">{{ userName }}</span>
        <button class="logout-btn" @click="Logout">IZIET</button>
      </div>
    </nav>

    <div class="favorites-container">
      <button class="back-btn" @click="goBack">
        <span class="back-arr">←</span> Atpakaļ
      </button>
      
      <div v-if="loading" class="loading">
        <p>Ielādē...</p>
      </div>

      <div v-else>
        <h2 class="fav-title">Mani favorīti</h2>
        <p class="fav-subtitle">{{ favorites.length }} iecienītākie īpašumi</p>
        <hr class="fav-rule" />

        <div v-if="favorites.length === 0" class="empty-favorites">
          <p>Jums vēl nav favorītu īpašumu.</p>
          <button class="browse-btn" @click="goToCatalog">Apskatīt īpašumus →</button>
        </div>

        <div v-else class="prop-grid">
          <div v-for="sludinajums in favorites" :key="sludinajums.SludinajumsID" class="pcard">
            <div class="pimg" @click="viewDetails(sludinajums)">
              <img :src="sludinajums.attels || '/images/default-house.jpg'" :alt="sludinajums.adrese" loading="lazy" />
              <span class="pbadge">{{ getTypeLabel(sludinajums.cat_slogan) }}</span>
              <button class="remove-fav" @click.stop="removeFromFavorites(sludinajums.SludinajumsID)">
                ❌
              </button>
            </div>
            <div class="pbody" @click="viewDetails(sludinajums)">
              <p class="ploc">{{ sludinajums.pilseta }}</p>
              <h3 class="pname">{{ sludinajums.adrese }}</h3>
              <div class="pfooter">
                <div class="pprice">{{ formatPrice(sludinajums.cena) }}<span>EUR</span></div>
                <span class="pview">Skatīt →</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'Favorites',
  data() {
    return {
      favorites: [],
      loading: true
    }
  },
  computed: {
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
    this.fetchFavorites()
  },
  methods: {
    async fetchFavorites() {
      this.loading = true
      try {
        const token = localStorage.getItem('auth_token')
        const response = await axios.get('http://localhost:8000/api/favorites', {
          headers: { 'Authorization': `Bearer ${token}` }
        })
        this.favorites = response.data
        console.log('Favorites loaded:', this.favorites)
      } catch (error) {
        console.error('Error fetching favorites:', error)
        if (error.response?.status === 401) {
          alert('Jūsu sesija ir beigusies. Lūdzu, pierakstieties vēlreiz.')
          this.handleLogout()
        }
      } finally {
        this.loading = false
      }
    },
    
    async removeFromFavorites(propertyId) {
      try {
        const token = localStorage.getItem('auth_token')
        await axios.delete(`http://localhost:8000/api/favorites/${propertyId}`, {
          headers: { 'Authorization': `Bearer ${token}` }
        })
        this.favorites = this.favorites.filter(fav => fav.SludinajumsID !== propertyId)
        alert('Īpašums izņemts no izlases!')
      } catch (error) {
        console.error('Error removing favorite:', error)
        alert('Kļūda! Lūdzu, mēģiniet vēlreiz.')
      }
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
    
    viewDetails(sludinajums) {
      this.$router.push(`/property/${sludinajums.SludinajumsID}`)
    },
    
    Logout() {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      delete axios.defaults.headers.common['Authorization']
      this.$router.push('/catalog')
      setTimeout(() => {
        location.reload()
      }, 100)
      
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
    
    goBack() {
      this.$router.back()
    }
  }
}
</script>

<style scoped>
:root {
  --cream: #f0ebe2;
  --dark: #1a1a18;
  --fb: 'Encode Sans Semi Expanded', sans-serif;
  --fd: 'Playfair Display', Georgia, serif;
}

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

.nav-links li button:hover,
.nav-links li button.active {
  opacity: 1;
}

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

.favorites-page {
  width: 100%;
  min-height: 100vh;
  background: #f0ebe2;
  padding-top: 100px;
  padding-bottom: 80px;
}

.favorites-container {
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
  margin-bottom: 48px;
  transition: color 0.25s ease;
}

.back-btn:hover {
  color: #1a1a18;
}

.back-arr {
  display: inline-block;
  transition: transform 0.28s ease;
}

.back-btn:hover .back-arr {
  transform: translateX(-4px);
}

.loading {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.9rem;
  color: rgba(26, 26, 24, 0.55);
}

.fav-title {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: clamp(2.2rem, 4vw, 3.4rem);
  font-weight: 500;
  font-style: italic;
  color: #1a1a18;
  margin-bottom: 10px;
}

.fav-subtitle {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.8rem;
  font-weight: 300;
  color: rgba(26, 26, 24, 0.55);
}

.fav-rule {
  border: none;
  border-top: 1px solid rgba(26, 26, 24, 0.12);
  margin: 32px 0;
}

.prop-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}

.pcard {
  background: white;
  cursor: pointer;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  overflow: hidden;
  position: relative;
}

.pcard:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.pimg {
  position: relative;
  aspect-ratio: 4 / 3;
  overflow: hidden;
  background: #d9d2c5;
}

.pimg img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.pcard:hover .pimg img {
  transform: scale(1.03);
}

.pbadge {
  position: absolute;
  bottom: 12px;
  left: 12px;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.6rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  background: rgba(0, 0, 0, 0.75);
  color: white;
  padding: 5px 10px;
  text-transform: uppercase;
  z-index: 1;
}

.remove-fav {
  position: absolute;
  top: 12px;
  right: 12px;
  background: rgba(0, 0, 0, 0.6);
  border: none;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  cursor: pointer;
  font-size: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.25s ease;
  z-index: 2;
}

.remove-fav:hover {
  background: rgba(0, 0, 0, 0.9);
  transform: scale(1.1);
}

.pbody {
  padding: 20px 20px 24px 20px;
  background: white;
}

.pname {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 1.2rem;
  font-weight: 500;
  color: #1a1a18;
  margin: 0 0 8px 0;
  line-height: 1.3;
}

.ploc {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
  font-weight: 500;
  letter-spacing: 0.08em;
  color: rgba(26, 26, 24, 0.55);
  text-transform: uppercase;
  margin: 0 0 16px 0;
}

.pfooter {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  border-top: 1px solid rgba(26, 26, 24, 0.1);
  padding-top: 16px;
}

.pprice {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1a1a18;
}

.pprice span {
  font-size: 0.7rem;
  font-weight: 500;
  margin-left: 4px;
  color: rgba(26, 26, 24, 0.5);
}

.pview {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.65rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  color: rgba(26, 26, 24, 0.5);
  text-transform: uppercase;
  transition: color 0.2s ease;
}

.pcard:hover .pview {
  color: #1a1a18;
}

.empty-favorites {
  text-align: center;
  padding: 80px 20px;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 1rem;
  color: rgba(26, 26, 24, 0.55);
}

.browse-btn {
  margin-top: 20px;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  background: #1a1a18;
  color: #f0ebe2;
  border: none;
  padding: 12px 24px;
  cursor: pointer;
  transition: opacity 0.25s ease;
}

.browse-btn:hover {
  opacity: 0.85;
}

@media (max-width: 960px) {
  .favorites-container {
    padding: 0 24px;
  }
  .prop-grid {
    grid-template-columns: 1fr;
  }
  .topnav {
    padding: 0 24px;
  }
  .nav-links {
    gap: 24px;
  }
}

@media (max-width: 640px) {
  .remove-fav {
    width: 28px;
    height: 28px;
    font-size: 0.9rem;
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