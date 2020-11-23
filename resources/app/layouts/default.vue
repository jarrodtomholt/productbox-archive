<template>
    <div class="h-screen w-screen flex flex-col flex-1 items-center justify-center bg-gray-100 font-sans">
        <div v-if="$fetchState.pending" class="flex flex-col flex-1">
            <div class="flex flex-1 items-center justify-center">
                <div class="loader text-pink-600">Loading</div>
            </div>

            <p class="pb-8 text-gray-400 font-light">
                Powered by <span class="text-pink-600">ProductBox</span> &copy; 2020
            </p>
        </div>
        <div v-else>
            <Nuxt />
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    computed: {
        ...mapGetters({
            settings: 'settings/all',
        }),
    },
    watch: {
        settings(value) {
            if (!value) {
                return
            }

            document.documentElement.style.setProperty('--primaryColor', value.theme.primaryColor)
            document.documentElement.style.setProperty('--secondaryColor', value.theme.secondaryColor)
        },
    },
    async fetch () {
        this.$axios.defaults.baseURL = `${window.location.origin}/api`

        return this.$axios.get('/').then(response => {
            this.$store.dispatch('categories/store', response.data.categories)
            this.$store.dispatch('settings/store', response.data.settings)
        })
    },
    fetchOnServer: false,
    fetchDelay: 500,
}
</script>
