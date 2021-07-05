<template>
  <!-- this needs a `z-index` (`tw-z-10`) to place it above
       `div.form span.ferror` which has `position: relative;` -->
  <div
    v-if="isActive"
    class="tw-z-10 tw-bg-opacity-75 tw-bg-black tw-fixed tw-inset-0 tw-w-full tw-h-screen tw-flex tw-items-center tw-justify-center"
    @click.self="$emit('cancel')"
  >
    <button
      aria-label="close"
      class="tw-absolute tw-top-0 tw-right-0 tw-text-xl tw-text-gray-500 tw-font-bold tw-my-2 tw-mx-4"
      @click.prevent="$emit('cancel')"
    >
      x
    </button>
    <div class="tw-w-full tw-max-w-2xl tw-bg-white tw-shadow-lg tw-rounded-lg tw-p-4">
      <header class="tw-border-b-2">
        <h1 class="tw-text-xl">
          {{ title }}
        </h1>
      </header>
      <div class="tw-p-4">
        <slot name="contents" />
      </div>
      <footer class="tw-flex tw-border-t-2">
        <button
          v-if="confirmLabel"
          class="tw-w-1/2 tw-text-white tw-bg-gray-700 hover:tw-bg-green-500 tw-rounded tw-p-1 tw-m-2"
          @click="$emit('confirm')"
        >
          {{ confirmLabel }}
        </button>
        <button
          v-if="cancelLabel"
          class="tw-w-1/2 tw-text-white tw-bg-gray-700 hover:tw-bg-red-500 tw-rounded tw-p-1 tw-m-2"
          @click="$emit('cancel')"
        >
          {{ cancelLabel }}
        </button>
      </footer>
    </div>
  </div>
</template>

<script>
export default {
    'name': 'DialogModal',
    'props': {
        'isActive': {
            'type': Boolean,
            'default': false,
        },
        'title': {
            'type': String,
            'required': true,
        },
        'cancelLabel': {
            'type': String,
            'default': 'Cancel',
        },
        'confirmLabel': {
            'type': String,
            'default': 'Confirm',
        },
    },
}
</script>