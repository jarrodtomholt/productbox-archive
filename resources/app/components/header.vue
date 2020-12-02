<template>
    <header class="flex items-center justify-between py-6 px-4">
        <div class="flex flex-row items-center space-x-4">
            <img src="https://www.fillmurray.com/200/300" class="h-24 rounded-lg">
            <h1 class="text-gray-800 font-semibold tracking-wide text-3xl">{{ settings.name }}</h1>
        </div>
        <div class="flex flex-row items-center space-x-8">
            <Status />
            <User />
            <Cart />
        </div>
    </header>
</template>

<script>
import { mapGetters } from 'vuex'
import Status from '~/components/status'
import User from '~/components/user'
import Cart from '~/components/cart'

export default {
    name: 'Header',
    components: {
        Status,
        User,
        Cart,
    },
    computed: {
        ...mapGetters({
            settings: 'settings/all',
        }),
    },
}
</script>
