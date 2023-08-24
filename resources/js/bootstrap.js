window._ = require("lodash");
try {
    window.$ = window.jQuery = require("jquery");
} catch (e) {
    console.log(e);
}

window.axios = require("axios");

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.withCredentials = true;

// import Echo from "laravel-echo";
// import Pusher from "pusher-js";

// window.Pusher = Pusher;
// window.Echo = new Echo({
//     broadcaster: "pusher",
//     key: window.pusher_key,
//     cluster: window.pusher_cluster,
//     forceTLS: true,
//     authEndpoint: "/api/admin/broadcasting/auth",
// });

// import Echo from '@ably/laravel-echo';

// window.Ably = require('ably');

// Create new echo client instance using ably-js client driver.
// window.Echo = new Echo({
//     broadcaster: 'ably',
//     key: window.ably_key,
//     authEndpoint: '/api/admin/broadcasting/auth',
//     auth: {
//         headers: {
//             'X-CSRF-TOKEN': window.Laravel.csrfToken,
//         },
//     },
// });

// // public channel
// window.Echo.channel("notifications-channel").listen(
//     ".NotificationEvent",
//     (data) => {
//         console.log(data);
//     }
// );

// window.Echo.channel("user." + window.Laravel.user).listen(
//     ".NotificationEvent",
//     (e) => {
//         console.log("e");
//     }
// );

// private channel
// window.Echo.private("user." + window.Laravel.user).listen(
//     ".NotificationEvent",
//     (e) => {
//         console.log("e");
//     }
// );
