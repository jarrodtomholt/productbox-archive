<template>
    <div class="h-screen w-screen flex flex-col overflow-hidden font-sans bg-gray-50">
        <Loader v-if="$fetchState.pending" />
        <div v-else class="flex flex-col flex-1 overflow-hidden font-sans">
            <header class="flex flex-col flex-shrink-0 pt-2 z-20 bg-indigo-600">
                <div class="flex flex-row flex-1 items-center justify-between">
                    <button @click="menuOpen = !menuOpen" class="p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                    </button>
                    <h2 class="flex-1 px-1 text-center text-sm font-medium leading-tight captialize truncate">
                        Page Title
                    </h2>
                    <button class="p-3 relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="absolute top-2 right-2 w-4 h-4 rounded-full bg-indigo-600 border-2 border-white"></span>
                    </button>
                </div>

                <div class="fixed inset-0 flex" :class="{ 'pointer-events-none' : menuOpen === false }">
                    <transition
                        enter-active-class="transition-opacity ease-linear duration-300"
                        enter-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="transition-opacity ease-linear duration-300"
                        leave-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-show="menuOpen" class="fixed inset-0" aria-hidden="true">
                            <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
                        </div>
                    </transition>
                    <transition
                        enter-active-class="transition ease-in-out duration-300 transform"
                        enter-class="-translate-x-full"
                        enter-to-class="translate-x-0"
                        leave-active-class="transition ease-in-out duration-300 transform"
                        leave-class="translate-x-0"
                        leave-to-class="-translate-x-full"
                    >
                        <div v-show="menuOpen" class="relative flex-1 flex flex-col max-w-xs w-full py-2 bg-white">
                            <div class="absolute top-0 right-0 -mr-12 pt-5">
                                <button @click="menuOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                                    <span class="sr-only">Close menu</span>
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-5 px-3 flex-1 h-0 overflow-y-auto">
                                <p>menu here</p>
                            </div>
                            <div class="flex-shrink-0 w-14" aria-hidden="true"></div>
                        </div>
                    </transition>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto">
                <Nuxt />
            </main>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Loader from '~/components/loader'

export default {
    name: 'defaultLayout',
    components: {
        Loader,
    },
    computed: {
        ...mapGetters({
            settings: 'settings/all',
            categories: 'categories/all',
        }),
    },
    data() {
        return {
            menuOpen: false,
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
