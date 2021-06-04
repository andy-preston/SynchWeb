<template>
  <section class="content">
    <h1>Send Feedback</h1>

    <p class="help">
      This page is for sending feedback about the
      ISPyB web interface to the developers, not for your beamtime!
      Please use UAS to report feedback for your visits
    </p>

    <form
      novalidate
      :class="{loading: isLoading}"
    >
      <div class="form">
        <ul>
          <li>
            <label>Your Name</label>
            <input
              v-model="name"
              v-validate="'required'"
              type="text"
              name="name"
              :class="{ferror: errors.has('name')}"
            >
            <span
              v-if="errors.has('name')"
              class="errormesage ferror"
            >{{ errors.first('name') }}</span>
          </li>
          <li>
            <label>Your Email Address</label>
            <input
              v-model="email"
              v-validate="'required|email'"
              name="email"
              type="email"
              :class="{ferror: errors.has('email')}"
            >
            <span
              v-if="errors.has('email')"
              class="errormesage ferror"
            >{{ errors.first('email') }}</span>
          </li>
          <li>
            <label>Feedback</label>
            <textarea
              v-model="feedback"
              v-validate="'required'"
              name="feedback"
              :class="{ferror: errors.has('feedback')}"
            />
            <span
              v-if="errors.has('feedback')"
              class="errormesage ferror"
            >{{ errors.first('feedback') }}</span>
          </li>
        </ul>

        <button
          name="submit"
          value="1"
          type="submit"
          class="button submit"
          @click.prevent="onSubmit"
        >
          Send Feedback
        </button>
      </div>
    </form>
  </section>
</template>

<script>
    import Vue from 'vue'
    import VeeValidate from 'veevalidate'
    // Promise is not used, but required for IE if we want to use vee-validate
    // eslint-disable-next-line no-unused-vars
    import Promise from 'promise'
    import FeedbackModel from 'modules/feedback/models/feedback'

    Vue.use(VeeValidate)

    export default {
        name: 'Feedback',
        data: function() {
            return {
                name: "",
                email: "",
                feedback: "",
                isLoading: false
            }
        },
        methods: {
            // With new build and (IE polyfill) we could use
            // Object.assign() to reset all data to initial state
            // Using the method below is simple alternative that
            // allows us to clear form data after submission
            resetForm: function() {
                this.name = ""
                this.email = ""
                this.feedback = ""

                // To reset form validation, we should wait for next tick
                // Vue reactivity means the DOM will not be updated immediately
                this.$nextTick(function() {
                    this.$validator.reset()
                })
            },
            onSubmit: function() {
                let self = this

                this.$validator.validateAll().then(function(result) {
                    if (result) {
                        self.submitFeedback()
                    } else {
                        console.log("Form submission prevented, validation failed");
                    }
                });
            },
            submitFeedback: function() {
                this.isLoading = true

                let model = new FeedbackModel({
                    "name": this.name,
                    "email": this.email,
                    "feedback": this.feedback
                })

                let self = this

                // Passing {} as first argument means send all model data
                model.save({}, {
                    // eslint-disable-next-line no-unused-vars
                    success: function(model, response, options) {
                        // Indicate success and reset form
                        app.message({ "message": "Feedback successfully submitted" })
                        self.isLoading = false
                        self.resetForm()
                    },
                    // eslint-disable-next-line no-unused-vars
                    error: function(model, response, options) {
                        app.alert({ "message": "Something went wrong submitting this feedback, please try again" })
                        // If feedback failed, don't reset the form, just set loading to false
                        self.isLoading = false
                    }
                })
            }
        }
      }
</script>
