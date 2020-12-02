<template>
    <div v-click-away="close" class="z-10 sm:relative">
        <button @click="open = !open" class="h-14 w-14 inline-flex items-center justify-center border border-transparent leading-4 font-medium rounded-full shadow-sm transition ease-out duration-100 overflow-hidden bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
            <span v-if="user" class="inline-flex h-full w-full items-center justify-center text-gray-900">
                {{ user.name|initials }}
            </span>
            <svg v-else class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </button>

        <div v-if="user">
            <transition enter-active-class="transition ease-out duration-100" enter-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                <div v-if="open" class="origin-top-right-0 absolute w-screen right-0 mt-2 sm:w-56 sm:rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                    <div class="px-4 py-3">
                        <p class="text-indigo-600 text-sm font-medium truncate">
                            {{ user.name }}
                        </p>
                        <p class="text-sm font-light text-gray-900 truncate">
                            {{ user.email }}
                        </p>
                    </div>
                    <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">Account settings</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">Support</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">License</a>
                    </div>
                    <div class="py-1">
                        <form @submit.prevent="logout">
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" role="menuitem">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </transition>
        </div>

        <div v-else>
            <transition enter-active-class="transition ease-out duration-100" enter-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                <div v-if="open" class="origin-top-right absolute right-0 mt-2 w-screen sm:max-w-md sm:px-0">
                    <div class="bg-white sm:rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden">
                        <div class="flex flex-1 items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
                            <div class="max-w-md w-full space-y-8">
                                <div>
                                    <h2 class="mt-2 text-center text-xl font-extrabold text-gray-900">
                                        Sign in to your productbox account
                                    </h2>
                                </div>
                                <form @submit.prevent="login" class="mt-8 space-y-6">
                                    <input type="hidden" name="remember" value="true">
                                    <div class="rounded-md shadow-sm -space-y-px">
                                        <div>
                                            <label for="email-address" class="sr-only">Email address</label>
                                            <input v-model="email" id="email-address" name="email" type="email" autocomplete="email" required class="appearance-none rounded-none relative block w-full p-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Email address">
                                        </div>
                                        <div>
                                            <label for="password" class="sr-only">Password</label>
                                            <input v-model="password" id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none rounded-none relative block w-full p-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password">
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end">
                                        <div class="text-sm">
                                            <a href="#" class="font-light text-indigo-600 hover:text-indigo-500">
                                                Forgot your password?
                                            </a>
                                        </div>
                                    </div>

                                    <div>
                                        <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 transition duration-300 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                                <svg class="h-5 w-5 text-indigo-500 transition duration-300 group-hover:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                            Sign in
                                        </button>
                                    </div>
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
                                    <div>
                                        <a href="#" class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            Register
                                        </a>
                                    </div>
                                </form>
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
    filters: {
        initials(name) {
            return name.match(/(^\S\S?|\b\S)?/g).join("").match(/(^\S|\S$)?/g).join("").toUpperCase()
        }
    },
    computed: {
        ...mapGetters({
            user: 'auth/user',
        }),
    },
    data() {
        return {
            open: false,
            email: 'user@productbox.test',
            password: 'password',
        }
    },
    methods: {
        login() {
            this.$axios.post('https://auth.productbox.test/api/login', {
                email: this.email,
                password: this.password,
            }).then(response => {
                this.open = false
                this.$store.dispatch('auth/store', response)
            })
        },
        logout() {
            this.$axios.delete('https://auth.productbox.test/api/logout').then(response => {
                this.open = false
                this.$store.dispatch('auth/store', response)
            })
        },
        close() {
            this.open = false
        },
    },
    async fetch() {
        await this.$axios.get('https://auth.productbox.test/api/user').then(response => {
            this.$store.dispatch('auth/store', response)
        })
    },
    fetchOnServer: false,
    fetchDelay: 500,
}
</script>
