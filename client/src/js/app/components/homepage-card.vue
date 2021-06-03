<template>
  <context-colour-card
    class="tw-flex-col tw-mb-4 tw-text-center tw-shadow"
    :class="classesForThreeUp(threeUp)"
    :level="level"
  >
    <p v-if="icon != ''">
      <i
        class="fa fa-4x"
        :class="icon"
      />
    </p>
    <p class="tw-text-2xl tw-py-2">
      {{ title }}
    </p>
    <div class="tw-text-left tw-text-sm">
      <p
        v-for="message in messages"
        class="tw-py-2"
      >
        {{ message }}
      </p>
    </div>
  </context-colour-card>
</template>

<script>
import ContextColourCard from './context-colour-card.vue'

export default {
    'name': 'HomepageCard',
    'components': {
        ContextColourCard,
    },
    props: {
        'icon': {
            'type': String,
            'default': '',
        },
        'title': {
            'type': String,
            'required': true,
        },
        'messages': {
            'type': Array,
            'required': true,
        },
        'level': {
            'type': String,
            'default': '',
        },
        'threeUp': Boolean,
    },
    'methods': {
        'classesForThreeUp': function(threeUp) {
            return threeUp ? 'md:tw-m-4 tw-w-full md:tw-w-1/3' : ''
        },
    },
}
</script>
