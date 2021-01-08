<template>
    <section class="content">
        <h1>Add Simple Sample</h1>
        <p class="help">This page allows you to add all sample information for one or more samples in a single transaction</p>
        <simple-sample v-if="ready" :model="model"></simple-sample>
    </section>
</template>

<script>
import SimpleSample from 'modules/types/xpdf/samples/views/vue-simplesample.vue'
import Protein from 'models/protein'

import VeeValidate from 'veevalidate'

Vue.use(VeeValidate)

VeeValidate.Validator.extend('closeExp', {
    getMessage: field => field+ ' must have correctly closed brackets',
    validate: value => {
        var count = 0
        for(var i=0;i<value.length;i++){
            if(value.charAt(i) === '(')
                count++
            else if(value.charAt(i) === ')'){
                if(count === 0)
                    return false
                else
                    count--
            }
        }
        if(count === 0)
            return true
        else
            return false
    }
})


export default {
    name: 'simple-sample-add-wrapper',
    components: {
        'simple-sample': SimpleSample
    },
    props: {
        'pid': Number,
    },
    data: function() {
        return {
            ready: false,
            model: null,
        }
    },
    created: function() {
        this.model = new Protein({ PROTEINID: this.pid })

        // Start loading animation
        this.$store.commit('loading', true)

        // Fetch the model
        this.getModel().then((result) => {
            // Set breadcrumbs now we have the model
            this.setBreadcrumbs()

        }, (error) => {
            console.log(this.$options.name + " Error getting model " + error.msg)
            app.alert({ title: 'Error getting model', message: error.msg})
        }).finally( () => { 
            // Only render when complete and stop loading animation
            this.$store.commit('loading', false)
            // Not convinced we still need to use the ready flag to delay rendering...
            this.ready = true
        })
    },
    methods: {
        getModel: function() {
            // self so we can use the stored custom error message (as in original controller)
            let self = this

            return new Promise((resolve) => {
                this.model.fetch({
                    success: function(model) {
                        resolve(true)
                    },
                    error: function() {
                        console.log(self.$options.name + " Wrapper error getting model ")
                        resolve({msg: self.error})
                    },
                })   

            })
        },
        // Set Breadcrumbs 
        setBreadcrumbs: function() {
            this.bc = [{ title: 'Samples', url: '/xsamples' }]
            this.bc.push({ title: 'Add' })
        },
    }
}
</script>