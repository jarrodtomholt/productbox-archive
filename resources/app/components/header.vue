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
            <User />
            <Cart />
        </div>
    </header>
</template>

<script>
import { mapGetters } from 'vuex'
import moment from 'moment'
import User from '~/components/user'
import Cart from '~/components/cart'

export default {
    name: 'Header',
    components: {
        User,
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
