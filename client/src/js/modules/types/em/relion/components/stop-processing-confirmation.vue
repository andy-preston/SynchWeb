<template>
  <modal-dialog
    :is-active="isActive"
    :message="message"
    title="Are You Sure"
    confirm-label="Yes"
    cancel-label="No"
    @confirm="$emit('confirm', jobId)"
    @cancel="$emit('cancel')"
  />
</template>

<script>
    import ModalDialog from 'app/components/modal-dialog.vue'

    export default {
        'name': 'StopProcessingConfirmation',
        'components': { ModalDialog },
        'props': {
            'isActive': {
                'type': Boolean,
                'default': false,
            },
            'jobId': {
                'type': Number,
                'required': true,
            },
        },
        'computed': {
            'message': function() {
                return 'Are you sure you want to stop processing job ' +
                    this.jobId + '?'
            },
        },
    }
</script>
