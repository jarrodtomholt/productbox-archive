<template>
    <div>
        <button @click="open = !open" class="inline-block h-14 w-14 rounded-full overflow-hidden bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
            <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </button>

        <div class="fixed z-10 inset-0 overflow-y-auto" :class="{ 'pointer-events-none' : !open }">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <transition enter-active-class="ease-out duration-300" enter-class="opacity-0" enter-to-class="opacity-100" leave-active-class="ease-in duration-200" leave-class="opacity-100" leave-to-class="opacity-0">
                    <div v-if="open" class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div @click="open = false" class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                </transition>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">
                    &#8203;
                </span>

                <transition enter-active-class="ease-out duration-300" enter-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to-class="opacity-100 translate-y-0 sm:scale-100" leave-active-class="ease-in duration-200" leave-class="opacity-100 translate-y-0 sm:scale-100" leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div v-if="open" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden h-screen w-screen shadow-xl transform transition-all sm:h-auto sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                        <div class="flex flex-col justify-center py-12 sm:px-6 lg:px-8">
                            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                                    Sign in to your account
                                </h2>
                            </div>

                            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                                <form @submit.prevent="login" class="space-y-6" action="#" method="POST">
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">
                                            Email address
                                        </label>
                                        <div class="mt-1">
                                            <input v-model="email" id="email" name="email" type="email" autocomplete="email" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700">
                                            Password
                                        </label>
                                        <div class="mt-1">
                                            <input v-model="password" id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                                                Remember me
                                            </label>
                                        </div>

                                        <div class="text-sm">
                                            <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                                                Forgot your password?
                                            </a>
                                        </div>
                                    </div>

                                    <div>
                                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Sign in
                                        </button>
                                    </div>
                                </form>

                                <div class="mt-6">
                                    <div class="relative">
                                        <div class="absolute inset-0 flex items-center">
                                            <div class="w-full border-t border-gray-300"></div>
                                        </div>
                                        <div class="relative flex justify-center text-sm">
                                            <span class="px-2 bg-white text-gray-500">
                                                Or
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex-1">
                                        <button type="button" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            Register
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </transition>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    name: 'User',
    computed: {
        ...mapGetters({
            user: 'auth/user',
        }),
    },
    data() {
        return {
            open: false,
            email: null,
            password: null
        }
    },
    methods: {
        login() {
            this.$axios.post('https://auth.productbox.test/api/login', {
                email: this.email,
                password: this.password,
            }).then(response => {
                this.$store.dispatch('auth/store', response)
            })
        }
    },
    async fetch() {
        await this.$axios.get('https://auth.productbox.test/api/user').then(response => {

        })
    },
    fetchOnServer: false,
    fetchDelay: 500,
}
</script>
