// Lazy load vue components
const FeedbackView = () => import(/* webpackChunkName: "help" */ 'modules/feedback/components/feedback-view.vue')

let routes = [
{
    'path': '/feedback',
    'name': 'feedback',
    'component': FeedbackView,
},
]

export default routes