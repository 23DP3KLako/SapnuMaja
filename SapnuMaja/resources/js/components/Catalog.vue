<template>
   <nav class="topnav">
      <span class="nav-brand" @click="goToHome">Sāpņu Māja</span>
      <ul class="nav-links">
        <li><button @click="goToHome">Sākums</button></li>
        <li><button class="active">Kategorijas</button></li>
        <li><button @click="goToAbout">Par mums</button></li>
        <li v-if="isRegistered && !isAdmin"><button @click="goToFavorites">FAVORĪTI</button></li>
        <li v-if="isAdmin"><button @click="toggleAdminPanel" class="admin-btn">ADMIN</button></li>
      </ul>
      <div class="nav-right" v-if="isRegistered">
        <span class="user-name">{{ userName }}</span>
        <button class="logout-btn" @click="Logout">IZIET</button>
      </div>
      <div class="nav-right" v-else>
        <button class="login-btn" @click="goToLogin">PIERAKSTĪTIES</button>
      </div>
    </nav>

  <div class="catalog-page">
    <div class="catalog-body">
      <button class="back-btn" @click="goBack">
        <span class="back-arr">←</span> Atpakaļ
      </button>

       <!-- Админ панель (видна только админу) -->
      <div v-if="isAdmin && showAdminPanel" class="admin-panel">
        <div class="admin-panel-header">
          <h3> Administrācijas panelis</h3>
          <button class="close-panel" @click="showAdminPanel = false">✕</button>
        </div>
        
        <div class="admin-tabs">
          <button :class="{ active: adminTab === 'add' }" @click="adminTab = 'add'"> Pievienot</button>
          <button :class="{ active: adminTab === 'edit' }" @click="adminTab = 'edit'"> Rediģēt</button>
          <button :class="{ active: adminTab === 'categories' }" @click="adminTab = 'categories'"> Kategorijas</button>
        </div>

        <!-- Форма добавления -->
        <div v-if="adminTab === 'add'" class="admin-form">
          <h4>{{ editing ? 'Rediģēt īpašumu' : 'Pievienot jaunu īpašumu' }}</h4>
          <form @submit.prevent="submitProperty">
            <div class="form-grid">
              <div class="form-group">
                <label>Virsraksts *</label>
                <input type="text" v-model="propertyForm.virsraksts" required />
              </div>
              <div class="form-group">
                <label>Kategorija *</label>
                <select v-model="propertyForm.KategorijasID" required>
                  <option value="">Izvēlieties</option>
                  <option v-for="cat in kategorijas" :key="cat.id" :value="cat.id">
                    {{ cat.nosaukums }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Pilsēta *</label>
                <input type="text" v-model="propertyForm.pilseta" required />
              </div>
              <div class="form-group">
                <label>Rajons</label>
                <input type="text" v-model="propertyForm.rajons" />
              </div>
              <div class="form-group full-width">
                <label>Adrese *</label>
                <input type="text" v-model="propertyForm.adrese" required />
              </div>
              <div class="form-group">
                <label>Cena (EUR) *</label>
                <input type="number" v-model="propertyForm.cena" required />
              </div>
               <div class="form-group">
                <label>Platība (m²) *</label>
                <input type="number" step="0.01" v-model="propertyForm.platiba" required />
              </div>
              <div class="form-group">
                <label>Zemes platība (are)</label>
                <input type="number" step="0.01" v-model="propertyForm.zemes_platiba" />
              </div>
              <div class="form-group">
                <label>Istabu skaits *</label>
                <input type="number" v-model="propertyForm.istabu_skaits" required />
              </div>
              <div class="form-group">
                <label>Stāvu skaits *</label>
                <input type="number" v-model="propertyForm.stavu_skaits" required />
              </div>
               <div class="form-group">
                <label>Būvniecības gads</label>
                <input type="number" v-model="propertyForm.celsanas_gads" />
              </div>
              <div class="form-group">
                <label>Stāvoklis</label>
                <select v-model="propertyForm.stavoklis">
                  <option value="">Izvēlieties</option>
                  <option>Jauna būve</option>
                  <option>Labā stāvoklī</option>
                  <option>Viduvējā stāvoklī</option>
                  <option>Projekts</option>
                </select>
              </div>
               <div class="form-group full-width">
                <label>Apraksts</label>
                <textarea v-model="propertyForm.apraksts" rows="3"></textarea>
              </div>
              <div class="form-group full-width">
                <label>Īpašības (atdalīt ar komatu)</label>
                <input type="text" v-model="propertyForm.ipasibas" placeholder="Autostāvvieta, Kamīns, Kondicionieris" />
              </div>
              <div class="form-group full-width">
              <label>Galvenais attēls (URL) *</label>
                <div class="image-input-group">
                  <div class="image-select-row">
                    <select v-model="selectedMainImage" class="image-select">
                      <option value="">Izvēlieties bildi...</option>
                      <option v-for="img in mainImageOptions" :key="img" :value="img">
                        {{ img }}
                      </option>
                    </select>
                    <button type="button" @click="applyMainImagePath" class="apply-path-btn">Pievienot ceļu</button>
                  </div>
                  <input type="text" v-model="propertyForm.attels" placeholder="/images/house1.jpg" class="full-input" />
                  <small class="form-hint">Ceļš: {{ baseImagePath }}{{ selectedMainImage || 'house1.jpg' }}</small>
                </div>
              </div>
              <div class="form-group full-width">
                <label>Papildu fotogrāfijas</label>
                <div class="extra-images-list">
                  <div v-for="(img, index) in propertyForm.extraImages" :key="index" class="extra-image-item">
                    <div class="extra-image-inputs">
                      <select v-model="selectedExtraRooms[index]" class="small-select">
                        <option value="">Izvēlieties telpu...</option>
                        <option v-for="room in extraImageNameOptions" :key="room" :value="room">
                          {{ room  }}
                        </option>
                      </select>
                      <select v-model="selectedExtraExtension[index]" class="ext-select" @change="applyExtraImagePath(index)">
                        <option value=".jpg">.jpg</option>
                        <option value=".png">.png</option>
                        <option value=".jpeg">.jpeg</option>
                      </select>
                      <input type="text" v-model="propertyForm.extraImages[index]" :placeholder="`/images/house${propertyForm.MajasID || 'X'}/kitchen.jpg`" />
                      <button type="button" @click="applyExtraImagePath(index)" class="apply-mini-btn">+</button>
                      <button type="button" @click="removeExtraImage(index)" class="remove-img-btn">✕</button>
                    </div>
                  </div>
                </div>
                <button type="button" @click="addExtraImage" class="add-img-btn">+ Pievienot vēl vienu attēlu</button>
                <small class="form-hint">Izmantojiet dropdown, lai ātri izvēlētos telpu</small>
              </div>
              
            </div>
            <div class="form-actions">
              <button type="button" @click="resetForm" class="cancel-btn">Atcelt</button>
              <button type="submit" class="submit-btn">{{ saving ? 'Saglabā...' : 'Pievienot →' }}</button>
            </div>
          </form>
        </div>

        <!-- Список для редактирования -->
        <div v-if="adminTab === 'edit'" class="admin-edit-list">
          <h4>Rediģēt īpašumus</h4>
          <div class="search-mini">
            <input type="text" v-model="editSearch" placeholder="Meklēt pēc adreses..." />
          </div>
          <div class="edit-items">
            <div v-for="prop in editableProperties" :key="prop.SludinajumsID" class="edit-item">
              <img :src="prop.attels" class="edit-img" />
              <div class="edit-info">
                <strong>{{ prop.adrese }}</strong>
                <span>{{ prop.pilseta }} - {{ formatPrice(prop.cena) }} €</span>
              </div>
              <div class="edit-actions">
                <button @click="startEdit(prop)" class="edit-btn">✏️</button>
                <button @click="deleteProperty(prop.SludinajumsID)" class="delete-btn">🗑️</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Управление категориями -->
        <div v-if="adminTab === 'categories'" class="admin-categories">
          <h4>Kategorijas</h4>
          <div class="add-category-mini">
            <input type="text" v-model="newCategory" placeholder="Jauna kategorija" />
            <input type="text" v-model="newCategorySlug" placeholder="Slugs (privatmajas)" />
            <button @click="addCategory">Pievienot</button>
          </div>
          <div class="category-list">
            <div v-for="cat in kategorijas" :key="cat.id" class="category-item">
              <span>{{ cat.nosaukums }} ({{ cat.slogan }})</span>
              <button @click="deleteCategory(cat.id)" class="delete-small">🗑️</button>
            </div>
          </div>
        </div>
      </div>

        

      <div v-if="loading" class="loading">
        <p>Ielādē...</p>
      </div>
      <div v-else>
        <h2 class="cat-title-big">Ekskluzīvi īpašumi</h2>
        <p class="cat-subtitle">{{ filteredProperties.length }} pieejami īpašumi.</p>
        <hr class="cat-rule" />
        
        <div class="filter-bar">
            <button 
              class="fchip" 
              :class="{ active: activeFilter === 'all' }" 
              @click="activeFilter = 'all'"
            >
              VISI
            </button>
            <button 
              v-for="kategorija in kategorijas" 
              :key="kategorija.id" 
              class="fchip" 
              :class="{ active: activeFilter === kategorija.id}" 
              @click="activeFilter = kategorija.id"
            >
              {{ getCategoryLabel(kategorija.nosaukums) }}
            </button>
          </div>
        
          <div class="prop-grid">
          
            <div v-for="sludinajums in filteredProperties" :key="sludinajums.SludinajumsID" class="pcard" @click="viewDetails(sludinajums)">
              <div class="pimg">
                <img :src="sludinajums.attels || '/images/default-house.jpg'" :alt="sludinajums.adrese" loading="lazy" />
                <span class="pbadge">{{ getTypeLabel(sludinajums.cat_slogan) }}</span>
                <button 
                  v-if="isRegistered && !isAdmin" 
                  class="favorite-heart" 
                  :class="{ active: favorites[sludinajums.SludinajumsID] }"
                  @click.stop="toggleFavorite(sludinajums.SludinajumsID)"
                >
                  {{ favorites[sludinajums.SludinajumsID] ? '❤️' : '♡' }}
                </button>
              </div>
              <div class="pbody">
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
  name: 'Catalog',
  data() {
    return {
      sludinajumi: [],      // 👈 ДОМА ИЗ БАЗЫ
      Kategorijas: [],
      favorites: {},      
      activeFilter: 'all',
      loading: true,
      // Admin Panel
      showAdminPanel: false,
      adminTab: 'add',
      saving: false,
      editSearch: '',
      newCategory: '',
      newCategorySlug: '',
      editing: false, 
      
      selectedMainImage: '',
      selectedExtraRooms: {},
      selectedExtraExtension: {},
      baseImagePath: '/images/',
      baseExtraImagePath: '/imageshouse',

      originalAttels: '',
      originalExtraImages: [],
       // Доступные названия для главного фото
      mainImageOptions: [
        'house1.jpg',
        'house2.jpg', 
        'house3.jpg',
        'house4.jpg',
        'house5.jpg',
        'house6.jpg',
        'house7.jpg',
        'house8.jpg',
        'house9.jpg',
        'house10.jpg'
      ],
      extraImageNameOptions: [
        'kitchen',
        'living', 
        'bedroom',
        'bethroom',
        'terrace',
        'garden'
      ],
      // Расширения файлов
      imageExtensions: ['.jpg', '.png', '.jpeg'],
      
      propertyForm: {
        virsraksts: '',
        KategorijasID: '',
        pilseta: '',
        rajons: '',
        adrese: '',
        cena: '',
        platiba: '',
        zemes_platiba: '',
        istabu_skaits: '',
        stavu_skaits: '',
        celsanas_gads: '',
        stavoklis: '',
        apraksts: '',
        ipasibas: '',
        attels: '',
        extraImages: [''] 
      }
    }
  },
  computed: {
    filteredProperties() {
      console.log('Active filter:', this.activeFilter);
     console.log('All sludinajumi:', this.sludinajumi);
     console.log('Cat slogans:', this.sludinajumi.map(p => p.cat_slogan));

     if (this.activeFilter === 'all') {
        return this.sludinajumi;
      }
      
      const filtered = this.sludinajumi.filter(p => {
        // Преобразуем в числа для сравнения
        const filterId = Number(this.activeFilter);
        const propertyId = Number(p.kategorija_id);
        const match = propertyId === filterId;
        if (match) {
          console.log(`Matched: ${p.adrese} (kategorija_id: ${propertyId})`);
        }
        return match;
      });
      
      console.log('Filtered count:', filtered.length);
      return filtered;
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
    },

    editableProperties() {
      if (!this.editSearch) return this.sludinajumi
      return this.sludinajumi.filter(p => 
        p.adrese.toLowerCase().includes(this.editSearch.toLowerCase())
      )
    }
  

  },
  
  mounted() {
   
    this.fetchKategorijas() 
    this.fetchSludinajumi()

   

    if (this.isRegistered) {
      this.fetchFavorites()
    }

    const editId = sessionStorage.getItem('edit_property_id')
    if (editId && this.isAdmin) {
      const editData = sessionStorage.getItem('edit_property_data')
      if (editData) {
        const property = JSON.parse(editData)
        this.startEdit(property)
        this.showAdminPanel = true
        this.adminTab = 'add'
        sessionStorage.removeItem('edit_property_id')
        sessionStorage.removeItem('edit_property_data')
      }
    }
  },
  
  methods: {

    // Применить путь для главного фото
    applyMainImagePath() {
      if (this.selectedMainImage) {
        this.propertyForm.attels = this.baseImagePath + this.selectedMainImage
      }
    },

    // Применить путь для дополнительного фото
    applyExtraImagePath(index) {
      const room = this.selectedExtraRooms[index]
      const ext = this.selectedExtraExtension[index] || '.jpg'
      if (room) {
        const houseId = this.propertyForm.MajasID || '4'
        const cleanRoom = room.replace(/\.(jpg|jpeg|png)$/i, '')
        this.propertyForm.extraImages[index] = `/imageshouse${houseId}/${room}${ext}`
      }
    },

    // Добавить поле для дополнительного фото
    addExtraImage() {
      this.propertyForm.extraImages.push('')
      const newIndex = this.propertyForm.extraImages.length - 1
      // Инициализируем новые поля выбора
      this.$set(this.selectedExtraRooms, newIndex, '')
      this.$set(this.selectedExtraExtension, newIndex, '.jpg')  // ДОБАВЬТЕ ЭТУ СТРОКУ
    },

    removeExtraImage(index) {
      this.propertyForm.extraImages.splice(index, 1)
      // Удаляем соответствующие выбранные значения
      delete this.selectedExtraRooms[index]
      delete this.selectedExtraExtension[index]
    },

    fetchKategorijas() {
      axios.get('http://localhost:8000/api/kategorijas')
        .then(response => {
          this.kategorijas = response.data
          console.log('Kategorijas ielādētas:', this.kategorijas)
          console.log('Kategorijas:', this.kategorijas)
         console.log('Kategorijas ielādētas:', this.kategorijas)
        })
        .catch(error => {
          console.error('Kļūda ielādējot kategorijas:', error)
        })
    },

    fetchSludinajumi() {
      axios.get('http://localhost:8000/api/sludinajumi')
        .then(response => {
          this.sludinajumi = response.data
          console.log('Īpašumi ielādēti:', this.sludinajumi)
          this.loading = false 
        })
        .catch(error => {
          console.error('Kļūda ielādējot īpašumus:', error)
        })
    },

    async fetchFavorites() {
      try {
        const token = localStorage.getItem('auth_token')
        const response = await axios.get('http://localhost:8000/api/favorites', {
          headers: { 'Authorization': `Bearer ${token}` }
        })
        
        const favMap = {}
        response.data.forEach(fav => {
          favMap[fav.SludinajumsID] = true
        })
        this.favorites = favMap
      } catch (error) {
        console.error('Error fetching favorites:', error)
      }
    },

    async toggleFavorite(propertyId) {
      if (!this.isRegistered) {
        alert('Lūdzu, pierakstieties lai pievienotu izlasei!')
        return
      }
      
      try {
        const token = localStorage.getItem('auth_token')
        if (this.favorites[propertyId]) {
          await axios.delete(`http://localhost:8000/api/favorites/${propertyId}`, {
            headers: { 'Authorization': `Bearer ${token}` },
           
          })
          this.favorites = { ...this.favorites, [propertyId]: false }
          alert('Īpašums izņemts no izlases!')
        } else {
          await axios.post('http://localhost:8000/api/favorites', {
            sludinajums_id: propertyId
          }, {
            headers: { 'Authorization': `Bearer ${token}` }
          })
          this.favorites = { ...this.favorites, [propertyId]: true }
          alert('Īpašums pievienots izlasei!')
        }
      } catch (error) {
        console.error('Error toggling favorite:', error)
        if (error.response?.status === 401) {
          alert('Jūsu sesija ir beigusies. Lūdzu, pierakstieties vēlreiz.')
          this.Logout()
        } else {
          alert('Kļūda! Lūdzu, mēģiniet vēlreiz.')
        }
      }
    },

    toggleAdminPanel() {
      this.showAdminPanel = !this.showAdminPanel
      this.adminTab = 'add'
    },
    
    resetForm() {
      this.editing = false
      this.selectedMainImage = ''
      this.selectedExtraRooms = {}
      this.selectedExtraExtension = {}
      this.originalAttels = ''
      this.originalExtraImages = []
      this.propertyForm = {
        SludinajumsID: '',  // <--- ДОБАВЬТЕ
        MajasID: '', 
        virsraksts: '',
        KategorijasID: '',
        pilseta: '',
        rajons: '',
        adrese: '',
        cena: '',
        platiba: '',
        zemes_platiba: '',
        istabu_skaits: '',
        stavu_skaits: '',
        celsanas_gads: '',
        stavoklis: '',
        apraksts: '',
        ipasibas: '',
        attels: '',
        extraImages: ['']
      }
    },

    async submitProperty() {
      this.saving = true

      if (!this.propertyForm.pilseta) {
        alert('Lūdzu, ievadiet pilsētu!')
        this.saving = false
        return
      }
      
      if (!this.propertyForm.adrese) {
        alert('Lūdzu, ievadiet adresi!')
        this.saving = false
        return
      }
      
      if (!this.propertyForm.KategorijasID) {
        alert('Lūdzu, izvēlieties kategoriju!')
        this.saving = false
        return
      }
      try {
        const token = localStorage.getItem('auth_token')

          const extraImages = this.propertyForm.extraImages.filter(img => img && img.trim() !== '')

        const submitData = {
          virsraksts: this.propertyForm.virsraksts,
          KategorijasID: this.propertyForm.KategorijasID,
          pilseta: this.propertyForm.pilseta,
          rajons: this.propertyForm.rajons || '',
          adrese: this.propertyForm.adrese,
          cena: this.propertyForm.cena,
          platiba: this.propertyForm.platiba,
          zemes_platiba: this.propertyForm.zemes_platiba || null,
          istabu_skaits: this.propertyForm.istabu_skaits,
          stavu_skaits: this.propertyForm.stavu_skaits,
          celsanas_gads: this.propertyForm.celsanas_gads || null,
          stavoklis: this.propertyForm.stavoklis || '',
          apraksts: this.propertyForm.apraksts || '',
          ipasibas: this.propertyForm.ipasibas || '',
          attels: this.propertyForm.attels,
          extra_images: extraImages,
          deleted_main_image: this.originalAttels && this.originalAttels !== this.propertyForm.attels,  // Флаг: удалено ли главное фото
          original_extra_images: this.originalExtraImages || []
        }
        
        if (this.editing && this.propertyForm.SludinajumsID) {
          // ОБНОВЛЕНИЕ существующего объявления
          await axios.put(`http://localhost:8000/api/properties/${this.propertyForm.SludinajumsID}`, 
            submitData, {
            headers: { 'Authorization': `Bearer ${token}` }
          })
          alert('Īpašums veiksmīgi atjaunināts!')
        } else {
          // ДОБАВЛЕНИЕ нового объявления
          await axios.post('http://localhost:8000/api/properties', submitData, {
            headers: { 'Authorization': `Bearer ${token}` }
          })
          alert('Īpašums veiksmīgi pievienots!')
        }

        location.reload()

        await this.fetchSludinajumi()  // Обновляем объявления
        await this.fetchKategorijas()  
        
        this.resetForm()
        this.showAdminPanel = false 
    
      } catch (error) {
        console.error('Error saving property:', error)
        alert('Kļūda saglabājot īpašumu!')
      } finally {
        this.saving = false
      }
    },
    
    startEdit(property) {
   
      this.editing = true  // <--- ДОБАВЬТЕ ЭТУ СТРОКУ
      this.selectedMainImage = ''
      this.selectedExtraRooms = {}
      this.selectedExtraExtension = {}

      this.originalAttels = property.attels || ''
      this.originalExtraImages = []
  
      this.propertyForm = {
        SludinajumsID: property.SludinajumsID,  // <--- ДОБАВЬТЕ
        MajasID: property.MajasID,              // <--- ДОБАВЬТЕ
        virsraksts: property.virsraksts || '',
        KategorijasID: property.kategorija_id || '',
        pilseta: property.pilseta || '',
        rajons: property.rajons || '',
        adrese: property.adrese || '',
        cena: property.cena || '',
        platiba: property.platiba || '',
        zemes_platiba: property.zemes_platiba || '',
        istabu_skaits: property.istabu_skaits || '',
        stavu_skaits: property.stavu_skaits || '',
        celsanas_gads: property.celsanas_gads || '',
        stavoklis: property.stavoklis || '',
        apraksts: property.apraksts || '',
        ipasibas: property.ipasibas || '',
        attels: property.attels || '',
        extraImages: []
      }

      if (property.atteli && property.atteli.length > 0) {
        property.atteli.forEach((img, idx) => {
          this.propertyForm.extraImages.push(img.attels_fail)
          this.originalExtraImages.push(img.attels_fail)
          this.selectedExtraRooms[idx] = ''
          this.selectedExtraExtension[idx] = '.jpg'
        })
      } else {
        this.propertyForm.extraImages = ['']
        this.selectedExtraRooms[0] = ''
        this.selectedExtraExtension[0] = '.jpg'
      }
      this.adminTab = 'add'
      this.showAdminPanel = true

    },

    async deleteProperty(id) {
      if (!confirm('Vai tiešām vēlaties dzēst šo īpašumu?')) return
      try {
        const token = localStorage.getItem('auth_token')
        await axios.delete(`http://localhost:8000/api/properties/${id}`, {
          headers: { 'Authorization': `Bearer ${token}` }
        })
        
        await this.fetchSludinajumi()
        alert('Īpašums dzēsts!')

        location.reload()
      
      } catch (error) {
        console.error('Error deleting property:', error)
        alert('Kļūda dzēšot īpašumu!')
      }
    },

    async addCategory() {
      if (!this.newCategory) return
      try {
        const token = localStorage.getItem('auth_token')
        await axios.post('http://localhost:8000/api/categories', {
          nosaukums: this.newCategory,
          slogan: this.newCategorySlug || this.newCategory.toLowerCase().replace(/ /g, '-')
        }, {
          headers: { 'Authorization': `Bearer ${token}` }
        })
        await this.fetchKategorijas()
        this.newCategory = ''
        this.newCategorySlug = ''
        alert('Kategorija pievienota!')
        location.reload()
      } catch (error) {
        console.error('Error adding category:', error)
      }
    },

    async deleteCategory(id) {
      console.log('Deleting category ID:', id)
      
      // Проверка на валидность ID
      if (!id || id === 'undefined' || id === null) {
        alert('Kļūda: nepareizs kategorijas ID!')
        return
      }
      
      if (!confirm('Vai tiešām vēlaties dzēst šo kategoriju?')) return
      
      try {
        const token = localStorage.getItem('auth_token')
        const response = await axios.delete(`http://localhost:8000/api/categories/${id}`, {
          headers: { 'Authorization': `Bearer ${token}` }
        })
        
        if (response.data.success) {
          alert('Kategorija dzēsta!')
          location.reload()
        } else {
          alert(response.data.error || 'Kļūda dzēšot kategoriju!')
        }
      } catch (error) {
        console.error('Error deleting category:', error)
        if (error.response) {
          alert(error.response.data.error || 'Kļūda dzēšot kategoriju!')
        } else {
          alert('Kļūda savienojumā ar serveri!')
        }
      }
    },

    formatPrice(price) {
      if (!price) return '0'
      return Number(price).toLocaleString()
    },

    getCategoryLabel(nosaukums) {
      const labels = {
        'Privātmāja': 'PRIVĀTMĀJAS',
        'Dzīvokļi': 'DZĪVOKĻI',
        'Lauku īpašumi': 'DABĀ'
      }
      return labels[nosaukums] || nosaukums.toUpperCase()
    },

    // Преобразуем slug категории в формат для бейджа
    getTypeLabel(slogan) {
      const types = {
        'privatmaja': 'PRIVĀTMĀJA',
        'dzivoklis': 'DZĪVOKLIS',
        'lauki': 'LAUKU ĪPAŠUMS'
      }
      return types[slogan] || 'ĪPAŠUMS'
    },

    Logout() {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      delete axios.defaults.headers.common['Authorization']
      alert('Jūs esat izrakstījies')
      location.reload()
    },

    addExtraImage() {
      this.propertyForm.extraImages.push('')
    },

    removeExtraImage(index) {
      this.propertyForm.extraImages.splice(index, 1)
    },
    

    viewDetails(sludinajums) {
      this.$router.push(`/property/${sludinajums.SludinajumsID}`)
    },

    goBack() {
      this.$router.push('/')
    },

    goToHome() {
      this.$router.push('/')
    },

    goToAbout() {
      this.$router.push('/par-mums')
    },

    goToLogin() {
      this.$router.push('/property/0')
    },

    goToFavorites() {
      this.$router.push('/favorites')
    },
  }
}
</script>
<style scoped>

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

.nav-links li button, .admin-nav-btn {
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



.admin-panel {
  background: white;
  border: 1px solid rgba(26, 26, 24, 0.15);
  margin-bottom: 40px;
  overflow: hidden;
}

.admin-panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 24px;
  background: #1a1a18;
  color: #f0ebe2;
}

.admin-panel-header h3 {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 1.2rem;
  font-weight: 500;
  margin: 0;
}

.close-panel {
  background: none;
  border: none;
  color: #f0ebe2;
  font-size: 1.2rem;
  cursor: pointer;
}

.admin-tabs {
  display: flex;
  border-bottom: 1px solid rgba(26, 26, 24, 0.1);
  background: #f9f9f7;
}

.admin-tabs button {
  padding: 12px 24px;
  background: none;
  border: none;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
  font-weight: 600;
  cursor: pointer;
  color: rgba(26, 26, 24, 0.6);
}

.admin-tabs button.active {
  color: #1a1a18;
  border-bottom: 2px solid #1a1a18;
}

.admin-form {
  padding: 24px;
}

.extra-images-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 12px;
}

.extra-image-item {
  display: flex;
  gap: 10px;
  align-items: center;
}

.extra-image-item input {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid rgba(26, 26, 24, 0.15);
  font-family: Georgia, serif;
}

.remove-img-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  color: #c33;
  padding: 4px 8px;
}

.add-img-btn {
  background: none;
  border: 1px dashed rgba(26, 26, 24, 0.3);
  color: #1a1a18;
  padding: 8px 12px;
  cursor: pointer;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
  width: 100%;
  margin-top: 8px;
}

.form-hint {
  display: block;
  margin-top: 8px;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.65rem;
  color: rgba(26, 26, 24, 0.5);
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.form-group.full-width {
  grid-column: span 2;
}

.form-group label {
  display: block;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.65rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  margin-bottom: 6px;
  color: rgba(26, 26, 24, 0.6);
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid rgba(26, 26, 24, 0.15);
  font-family: Georgia, serif;
  font-size: 0.9rem;
}

.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 20px;
}

.submit-btn {
  background: #1a1a18;
  color: #f0ebe2;
  padding: 10px 24px;
  border: none;
  cursor: pointer;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
  font-weight: 600;
}

.cancel-btn {
  background: none;
  border: 1px solid rgba(26, 26, 24, 0.3);
  padding: 10px 24px;
  cursor: pointer;
}

.admin-edit-list {
  padding: 24px;
}

.search-mini {
  margin-bottom: 20px;
}

.search-mini input {
  padding: 8px 12px;
  width: 300px;
  border: 1px solid rgba(26, 26, 24, 0.15);
}

.edit-items {
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-height: 400px;
  overflow-y: auto;
}

.edit-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 12px;
  border: 1px solid rgba(26, 26, 24, 0.1);
}

.edit-img {
  width: 60px;
  height: 50px;
  object-fit: cover;
}

.edit-info {
  flex: 1;
}

.edit-info strong {
  display: block;
  font-family: Georgia, serif;
}

.edit-info span {
  font-size: 0.8rem;
  color: rgba(26, 26, 24, 0.6);
}

.edit-actions {
  display: flex;
  gap: 8px;
}

.edit-btn, .delete-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 4px 8px;
}

.admin-categories {
  padding: 24px;
}

.add-category-mini {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
}

.add-category-mini input {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid rgba(26, 26, 24, 0.15);
}

.add-category-mini button {
  background: #1a1a18;
  color: #f0ebe2;
  padding: 8px 20px;
  border: none;
  cursor: pointer;
}

.login-btn {
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.64rem;
  font-weight: 600;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  background: #1a1a18;
  color: #f0ebe2;
  border: none;
  padding: 6px 14px;
  cursor: pointer;
  border-radius: 4px;
}

.login-btn:hover {
  opacity: 0.85;
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


.catalog-page {
  width: 100%;
  min-height: 100vh;
  background: var(--cream, #f0ebe2);
  padding-top: 62px;
}

.catalog-body {
  padding: 60px 64px 80px;
  max-width: 1300px;
  margin: 0 auto;
}

.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-family: var(--fb, 'Encode Sans Semi Expanded', sans-serif);
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
  color: var(--dark, #1a1a18);
}

.back-arr {
  display: inline-block;
  transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
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

.cat-title-big {
  font-family: var(--fd, 'Playfair Display', Georgia, serif);
  font-size: clamp(2.2rem, 4vw, 3.4rem);
  font-weight: 500;
  font-style: italic;
  color: var(--dark, #1a1a18);
  margin-bottom: 10px;
  line-height: 1.15;
}

.cat-subtitle {
  font-family: var(--fb, 'Encode Sans Semi Expanded', sans-serif);
  font-size: 0.8rem;
  font-weight: 300;
  color: rgba(26, 26, 24, 0.55);
  line-height: 1.7;
  letter-spacing: 0.03em;
}

.cat-rule {
  border: none;
  border-top: 1px solid rgba(26, 26, 24, 0.12);
  margin: 32px 0;
}

.filter-bar {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 36px;
}

.fchip {
  font-family: var(--fb, 'Encode Sans Semi Expanded', sans-serif);
  font-size: 0.62rem;
  font-weight: 600;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  padding: 10px 20px;
  border: 1px solid rgba(26, 26, 24, 0.22);
  background: transparent;
  color: rgba(26, 26, 24, 0.55);
  cursor: pointer;
  transition: all 0.22s ease;
}

.fchip:hover {
  background: rgba(26, 26, 24, 0.06);
  color: var(--dark, #1a1a18);
}

.fchip.active {
  background: var(--dark, #1a1a18);
  color: var(--cream, #f0ebe2);
  border-color: var(--dark, #1a1a18);
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
  font-family: var(--fb, 'Encode Sans Semi Expanded', sans-serif);
  font-size: 0.6rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  background: rgba(0, 0, 0, 0.75);
  color: white;
  padding: 5px 10px;
  text-transform: uppercase;
  z-index: 1;
}

.favorite-heart {
  position: absolute;
  top: 12px;
  right: 12px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.9);
  border: none;
  cursor: pointer;
  font-size: 1.3rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.25s ease;
  z-index: 2;
  backdrop-filter: blur(4px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.favorite-heart:hover {
  transform: scale(1.1);
  background: white;
}

.favorite-heart.active {
  background: rgba(255, 255, 255, 0.95);
  color: #c33;
}

.favorite-heart.active:hover {
  transform: scale(1.1);
}

.pbody {
  padding: 20px 20px 24px 20px;
  background: white;
}

.pname {
  font-family: var(--fd, 'Playfair Display', Georgia, serif);
  font-size: 1.2rem;
  font-weight: 500;
  color: var(--dark, #1a1a18);
  margin: 0 0 8px 0;
  line-height: 1.3;
}

.ploc {
  font-family: var(--fb, 'Encode Sans Semi Expanded', sans-serif);
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
  margin-top: 0;
}

.pprice {
  font-family: var(--fb, 'Encode Sans Semi Expanded', sans-serif);
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--dark, #1a1a18);
}

.pprice span {
  font-size: 0.7rem;
  font-weight: 500;
  margin-left: 4px;
  color: rgba(26, 26, 24, 0.5);
}

.pview {
  font-family: var(--fb, 'Encode Sans Semi Expanded', sans-serif);
  font-size: 0.65rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  color: rgba(26, 26, 24, 0.5);
  text-transform: uppercase;
  transition: color 0.2s ease;
}

.pcard:hover .pview {
  color: var(--dark, #1a1a18);
}

/* Стили для выбора изображений */
.image-input-group {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.image-select-row {
  display: flex;
  gap: 10px;
  align-items: center;
}

.image-select {
  flex: 2;
  padding: 8px 12px;
  border: 1px solid rgba(26, 26, 24, 0.15);
  background: #f9f9f7;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.8rem;
}

.apply-path-btn {
  background: #1a1a18;
  color: #f0ebe2;
  border: none;
  padding: 8px 16px;
  cursor: pointer;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
}

.full-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid rgba(26, 26, 24, 0.15);
  font-family: Georgia, serif;
}

.extra-image-inputs {
  display: flex;
  gap: 8px;
  align-items: center;
  width: 100%;
  flex-wrap: wrap;
}

.ext-select {
  width: 70px;
  padding: 8px;
  border: 1px solid rgba(26, 26, 24, 0.15);
  background: #f9f9f7;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
}

.extra-url-input {
  flex: 2;
  min-width: 200px;
  padding: 8px;
  border: 1px solid rgba(26, 26, 24, 0.15);
  font-family: Georgia, serif;
}

.small-select {
  flex: 1;
  padding: 8px;
  border: 1px solid rgba(26, 26, 24, 0.15);
  background: #f9f9f7;
  font-family: 'Encode Sans Semi Expanded', sans-serif;
  font-size: 0.7rem;
  min-width: 100px;
}

.extra-image-inputs input {
  flex: 2;
  padding: 8px;
  border: 1px solid rgba(26, 26, 24, 0.15);
  font-family: Georgia, serif;
}

.apply-mini-btn {
  background: #1a1a18;
  color: #f0ebe2;
  border: none;
  width: 32px;
  height: 34px;
  cursor: pointer;
  font-size: 1.2rem;
}

.remove-img-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  color: #c33;
  width: 32px;
  height: 34px;
}

@media (max-width: 960px) {
  .catalog-body {
    padding-left: 24px;
    padding-right: 24px;
  }
  .prop-grid {
    grid-template-columns: 1fr;
  }
}
</style>