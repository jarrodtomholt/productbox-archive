<template>
    <div class="h-screen w-screen overflow-hidden bg-gray-50 font-sans">
        <Loader v-if="$fetchState.pending" />
        <div v-else>
            <Header />
            <Nuxt />

<div v-if="show" class="fixed bottom-0 inset-x-0 pb-2 sm:pb-5">
  <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
    <div class="p-2 rounded-lg bg-indigo-600 shadow-lg sm:p-3">
      <div class="flex items-center justify-between flex-wrap">
        <div class="w-0 flex-1 flex items-center">
          <span class="flex p-2 rounded-lg bg-indigo-800">
            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
            </svg>
          </span>
          <p class="flex items-center ml-3 font-medium text-white truncate">
            <span class="md:hidden">
              {{ settings.messageOfTheDay }}
            </span>
            <span class="hidden md:inline">
              {{ settings.messageOfTheDay }}
              <span class="ml-3 text-sm font-light">
                Lorem, ipsum dolor sit amet consectetur adipisicing elit. Praesentium, reiciendis.
              </span>
            </span>
          </p>
        </div>
        <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-2">
          <button @click="show = false" type="button" class="-mr-1 flex p-2 rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-white">
            <span class="sr-only">Dismiss</span>
            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Loader from '~/components/loader'
import Header from '~/components/header'

export default {
    name: 'defaultLayout',
    components: {
        Loader,
        Header,
    },
    computed: {
        ...mapGetters({
            settings: 'settings/all',
        }),
    },
    data() {
        return {
            show: true,
        }
    },
    async fetch () {
        await this.$axios.get('/').then(response => {
            this.$store.dispatch('categories/store', response.categories)
            this.$store.dispatch('settings/store', response.settings)
        })
    },
    fetchOnServer: false,
    fetchDelay: 500,
}
</script>
