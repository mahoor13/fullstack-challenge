<template>
    <div class="q-pa-md q-gutter-sm">
        <div  class="q-pa-md q-gutter-md">
            <div class="q-page q-layout-padding flex justify-center items-start">
                <q-list bordered padding class="q-stepper q-stepper--horizontal q-stepper--flat q-stepper--bordered" color="secondary" style="width:450px; max-width: 90vw;">
                    <q-item v-for="item in items" :key="item.id" clickable v-ripple>
                        <q-item-section avatar top>
                            <q-avatar icon="star" color="primary" text-color="white" />
                        </q-item-section>

                        <q-item-section @click="getForecastDetails(item)">
                            <q-item-label lines="1">{{ item.name }}</q-item-label>
                            <q-item-label caption>{{ item.email }}</q-item-label>
                        </q-item-section>

                        <q-item-section side>
                            <q-item-label caption>{{ item.temperature }}</q-item-label>
                        </q-item-section>

                        <q-item-section side>
                            <q-icon name="info" />
                        </q-item-section>
                    </q-item>
                </q-list>
            </div>
        </div>
  
      <q-dialog v-model="dialog" transition-hide="flip-up">
        <q-layout view="Lhh lpR fff" container class="bg-white">
          <q-header class="bg-primary">
            <q-toolbar>
              <q-toolbar-title padding style="padding: 1rem;">
                    <q-item-label lines="1">{{ details.name }}</q-item-label>
                    <q-item-label caption class="text-white">{{ details.email }}</q-item-label>
              </q-toolbar-title>
              <q-btn flat v-close-popup round dense icon="close" />
            </q-toolbar>
          </q-header>
  
          <q-footer class="bg-black text-white">
            <q-toolbar inset>
              <q-toolbar-title> </q-toolbar-title>
            </q-toolbar>
          </q-footer>
  
          <q-page-container>
            <q-page padding style="padding: 2rem;">
              
                    <q-item-label caption>Forecast:</q-item-label>
                    <q-item-label class="text-h6 text-secondary" style="padding: 0 1rem 1rem;">
                        {{ details.data.weather_info.forecast }}
                    </q-item-label>

                    <q-item-label caption>Temperature:</q-item-label>
                    <q-item-label class="text-h6 text-secondary" style="padding: 0 1rem 1rem;"> 
                        {{ details.data.weather_info.temperature }}°{{ details.data.weather_info.temperatureUnit }}
                    </q-item-label>
                
                    <q-item-label caption>Wind Speed:</q-item-label>
                    <q-item-label class="text-h6 text-secondary" style="padding: 0 1rem 1rem;">
                        {{ details.data.weather_info.windSpeed }} Km/h 
                        ({{ details.data.weather_info.windDirection }})
                    </q-item-label>
                
                    <q-item-label caption>Latitude:</q-item-label>
                    <q-item-label class="text-h6 text-secondary" style="padding: 0 1rem 1rem;">
                        {{ details.data.latitude }}
                    </q-item-label>
                
                    <q-item-label caption>Longitude:</q-item-label>
                    <q-item-label class="text-h6 text-secondary" style="padding: 0 1rem 1rem;">
                        {{ details.data.longitude }}
                    </q-item-label>
                
                    <q-item-label caption>Weather Service:</q-item-label>
                    <q-item-label class="text-h6 text-secondary" style="padding: 0 1rem 1rem;">
                        {{ details.data.weather_service }}
                    </q-item-label>
                
                    <q-item-label caption>Last Update:</q-item-label>
                    <q-item-label class="text-secondary" style="padding: 0 1rem 1rem;">
                        {{ details.data.last_update }}
                    </q-item-label>

            </q-page>
          </q-page-container>
        </q-layout>
      </q-dialog>
    </div>
  </template>
  

<script lang="ts">

import { defineComponent } from 'vue'
import { useQuasar } from 'quasar'

export default defineComponent({
name: 'UserList',
data() {
    return {
        dialog: false as boolean,
        items: [] as any[],
        details: null as {} | null,
        $q: null as {} | null,
    }
},
mounted() {
    this.$q = useQuasar()
    this.fetchData()
},
methods: {
    async fetchData() {
        const response = await fetch(`${import.meta.env.VITE_API_BASE}/v1/user/list`)
        const {data} = await response.json()
        if (!data) {

        }
        this.items = data
        this.getForecastList()
        setInterval(this.getForecastList, 10000);
    },
    async getForecastList() {
        const response = await fetch(`${import.meta.env.VITE_API_BASE}/v1/weather/list`)
        const {data} = await response.json()
        if (!data) {
            return
        }
        this.items.forEach((item, key) => {
            const temperature = data[item.id]
            if  (!temperature) {
                return;
            }
            item.temperature = temperature.value + '°' + temperature.unit
        })
    },
    async getForecastDetails(item: Object) {
        const response = await fetch(`${import.meta.env.VITE_API_BASE}/v1/weather/detail/${item.id}`)
        let {data} = await response.json()
        if (!data) {
            this.$q.notify({
                message: 'Information not available!',
                position: 'top-left',
            })
            return
        }
        this.dialog = true
        this.details = {
            data: data,
            name: item.name,
            email: item.email,
        }
        console.log(this.details)
    },
},
})
</script>

<style>
.list li {
    cursor: pointer;
    margin-bottom: 1rem;
    color: navy;
    padding: 0.2rem;
}
.list li:hover {
    color: blueviolet;
    padding-left: 0.4rem;
    transition: all ease 0.3s;
}
</style>