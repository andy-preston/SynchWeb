<template>
  <section id="home">
    <div class="tw-mx-auto">
      <hero-title />
      <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between">
        <homepage-card
          three-up="true"
          icon="fa-truck"
          title="Prepare for Beamtime"
          :messages="[
            'With SynchWeb you can create Shipments and use integrated DHL shipping services to get your samples to site.',
            'Your parcels or dewars are tracked across the facility so you can see where your samples are.',
            'While registering samples, you can specify data collection recipes for automated collection and processing.',
          ]"
        />
        <homepage-card
          three-up="true"
          icon="fa-clock-o"
          title="Monitor Experiment"
          :messages="[
            'SynchWeb provides a live activity stream of events on beamlines.',
            'You can observe the data collection process through webcam feeds of your session.',
            'Results from automated and downstream processing pipelines are displayed for initial assessment.',
          ]"
        />
        <homepage-card
          three-up="true"
          icon="fa-pie-chart"
          title="Process and analyze data"
          :messages="[
            'SynchWeb includes analysis and visualisation views from DIALS, autoPROC, results from Jupyter notebooks etc.',
            'Review electron density maps from Dimple and FastDP.',
            'Reprocess data from single or multiple data collections.',
          ]"
        />
      </div>

      <div class="md:tw-mx-4">
        <homepage-card
          title="Messages for Users"
          level="info"
          :messages="[
            'THIS IS JUST PLACEHOLDER TEXT - A RELEASE VERSION WOULD HAVE SOMETHING TO SAY HERE',
            'Running SynchWeb requires setting up a Linux, Apache, MariaDB and PHP (LAMP) software stack. If running in production you should configure your Apache and PHP to serve secure pages only. The steps below describe how to build the software so it is ready to deploy onto your target server.',
            'For development, a simple environment can be setup by using scripts provided here. They are not intended for production use but include scripts to automatically build and deploy the software on a local VM.',
            'If you make use of code from this repository, please reference: Fisher et al., J. Appl. Cryst. (2015). 48, 927-932, doi:10.1107/S1600576715004847 https://journals.iucr.org/j/issues/2015/03/00/fs5101/index.html',
          ]"
        />
      </div>

      <div class="tw-flex tw-flex-col w-w-full tw-m-2 tw-p-4">
        <p class="tw-text-center tw-text-lg tw-mb-4">
          <router-link
            to="/login"
            class="tw-underline"
          >
            Login
          </router-link>
          to view your proposals and sessions
        </p>
        <p
          v-if="dataCatalogue"
          class="tw-text-center tw-text-lg"
        >
          If you are looking for archived data please visit the data catalogue
          <a
            :href="dataCatalogue.url"
            class="tw-underline"
          >
            {{ dataCatalogue.name }}
          </a>
        </p>
      </div>
    </div>
  </section>
</template>

<script>
import Hero from 'app/components/herotitle.vue'
import HomepageCard from 'app/components/homepage-card.vue'
import EventBus from 'app/components/utils/event-bus.js'
import Config from 'config.json'

export default {
    name: 'Home',
    components: {
        'hero-title': Hero,
        HomepageCard,
    },
    data() {
        return {
            facility: 'Diamond',
            current_date: new Date(),
        }
    },
    computed: {
        isLoggedIn: function() {
            return this.$store.getters['auth/isLoggedIn']
        },
        dataCatalogue: function() {
            return Config.data_catalogue
        },
        skipHome: function() {
          return this.$store.state.skipHomePage
        }
    },
    created: function() {
        // let self = this
        // Reset proposal and associated type/model
        // We call the action so the store can handle the proposal type and model
        this.$store.dispatch('proposal/setProposal', null)
        EventBus.$emit('bcChange', [{title: '/', url: '/'}])

        if (this.isLoggedIn) {
            this.$router.push('current')
        } else if (this.skipHome) {
            this.$router.push('/login')
        }
    },
}
</script>
