import apiModule from 'modules/types/em/store/api-module'
import processingModule from 'modules/types/em/store/processing-module'

const emModule = {
    'namespaced': true,
    'state': {
        'selectedMovie': 0,
    },
    'getters': {
        'selectedMovie': function(
            state,
            getters, // eslint-disable-line no-unused-vars
            rootState // eslint-disable-line no-unused-vars
        ) {
            return state.selectedMovie
        },
    },
    'mutations': {
        'selectMovies': function(state, selectedMovie) {
            state.selectedMovie = selectedMovie
        },
    },
}

export default {
    'register': function(store) {
        const modules = {
            'em': emModule,
            'em/api': apiModule,
            'em/processing': processingModule,
        }
        Object.keys(modules).forEach(function(key) {
            if (!store.hasModule(key)) {
                store.registerModule(key, modules[key])
                console.log('registered storage module ' + key)
            }
        })
    },
    'unregister': function(store) {
        const modules = {
            'em/processing': processingModule,
            'em/api': apiModule,
            'em': emModule,
        }
        Object.keys(modules).forEach(function(key) {
            if (store.hasModule(key)) {
                store.unregisterModule(key)
                console.log('unregistered storage module ' + key)
            }
        })
    },
}
