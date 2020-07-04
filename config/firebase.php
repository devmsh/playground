<?php

return [
    /**
     * ------------------------------------------------------------------------
     * Credentials / Service Account
     * ------------------------------------------------------------------------
     *
     * In order to access a Firebase project and its related services using a
     * server SDK, requests must be authenticated. For server-to-server
     * communication this is done with a Service Account.
     *
     * If you don't already have generated a Service Account, you can do so by
     * following the instructions from the official documentation pages at
     *
     * https://firebase.google.com/docs/admin/setup#initialize_the_sdk
     *
     * Once you have downloaded the Service Account JSON file, you can use it
     * to configure the package.
     *
     * If you don't provide credentials, the Firebase Admin SDK will try to
     * autodiscover them
     *
     * - by checking the environment variable FIREBASE_CREDENTIALS
     * - by checking the environment variable GOOGLE_APPLICATION_CREDENTIALS
     * - by trying to find Google's well known file
     * - by checking if the application is running on GCE/GCP
     *
     * If no credentials file can be found, an exception will be thrown the
     * first time you try to access a component of the Firebase Admin SDK.
     *
     */
    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS'),

        /**
         * If you want to prevent the auto discovery of credentials, set the
         * following parameter to false. If you disable it, you must
         * provide a credentials file.
         */
        'auto_discovery' => true,
    ],

    /**
     * ------------------------------------------------------------------------
     * Firebase Realtime Database
     * ------------------------------------------------------------------------
     */

    'database' => [

        /**
         * In most of the cases the project ID defined in the credentials file
         * determines the URL of your project's Realtime Database. If the
         * connection to the Realtime Database fails, you can override
         * its URL with the value you see at
         *
         * https://console.firebase.google.com/u/1/project/_/database
         *
         * Please make sure that you use a full URL like, for example,
         * https://my-project-id.firebaseio.com
         */
        'url' => env('FIREBASE_DATABASE_URL'),

    ],

    'dynamic_links' => [

        /**
         * Dynamic links can be built with any URL prefix registered on
         *
         * https://console.firebase.google.com/u/1/project/_/durablelinks/links/
         *
         * You can define one of those domains as the default for new Dynamic
         * Links created within your project.
         *
         * The value must be a valid domain, for example,
         * https://example.page.link
         */
        'default_domain' => env('FIREBASE_DYNAMIC_LINKS_DEFAULT_DOMAIN')
    ],

    /**
     * ------------------------------------------------------------------------
     * Firebase Cloud Storage
     * ------------------------------------------------------------------------
     */

    'storage' => [

        /**
         * Your project's default storage bucket usually uses the project ID
         * as its name. If you have multiple storage buckets and want to
         * use another one as the default for your application, you can
         * override it here.
         */

        'default_bucket' => env('FIREBASE_STORAGE_DEFAULT_BUCKET'),

    ],

    /**
     * ------------------------------------------------------------------------
     * Caching
     * ------------------------------------------------------------------------
     *
     * The Firebase Admin SDK can cache some data returned from the Firebase
     * API, for example Google's public keys used to verify ID tokens.
     *
     */

    'cache_store' => env('FIREBASE_CACHE_STORE', 'file'),

];
