<template>
    <header class="flex items-center justify-between py-6 px-4">
        <div class="flex flex-row items-center space-x-4">
            <img src="https://www.fillmurray.com/200/300" class="h-24 rounded-lg">
            <h1 class="text-gray-800 font-semibold tracking-wide text-3xl">{{ settings.name }}</h1>
        </div>
        <div class="flex flex-row items-center space-x-8">
            <div class="px-4 text-right">
                <p class="capitalize text-gray-700 text-xl leading-snug font-bold">
                    {{ currentStatus }}
                </p>
                <p class="text-gray-400 text-md font-light">
                    {{ nextOpenTime }} to {{ nextCloseTime }} {{ nextOpenDate }}
                </p>
            </div>
            <span class="inline-block h-14 w-14 rounded-full overflow-hidden bg-gray-100">
                <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </span>
            <Cart />
        </div>
    </header>
</template>

<script>
import { mapGetters } from 'vuex'
import moment from 'moment'
import Cart from '~/components/cart'

export default {
    name: 'Header',
    components: {
        Cart,
    },
    computed: {
        ...mapGetters({
            settings: 'settings/all',
        }),
        currentStatus() {
            return this.settings.openingHours.isOpen ? 'open' : 'closed'
        },
        nextOpenDate() {
            return moment().calendar(this.settings.openingHours.nextOpen, {
                sameDay: '[Today]',
                lastDay: '[Tomorrow]',
                lastWeek: 'D-M-Y',
            });
        },
        nextOpenTime() {
            return moment(this.settings.openingHours.nextOpen).format('LT')
        },
        nextCloseTime() {
            return moment(this.settings.openingHours.nextClose).format('LT')
        }
    },
}
</script>
