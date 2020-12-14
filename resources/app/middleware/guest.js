export default function ({ store, redirect, route }) {
    if (store.getters['auth/token'] !== null) {
        return redirect('/')
    }
}
