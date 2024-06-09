import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import 'flowbite';
import 'chart.js';

import { Carousel } from 'flowbite';




const carouselElement = document.getElementById('default-carousel');

const items = [
    {
        position: 0,
        el: document.getElementById('carousel-item-1'),
    },
    {
        position: 1,
        el: document.getElementById('carousel-item-2'),
    },
    {
        position: 2,
        el: document.getElementById('carousel-item-3'),
    },
    {
        position: 3,
        el: document.getElementById('carousel-item-4'),
    },
    {
        position: 4,
        el: document.getElementById('carousel-item-5'),
    },
    {
        position: 5,
        el: document.getElementById('carousel-item-6'),
    },
    {
        position: 6,
        el: document.getElementById('carousel-item-7'),
    },
    {
        position: 7,
        el: document.getElementById('carousel-item-8'),
    },
    {
        position: 8,
        el: document.getElementById('carousel-item-9'),
    },
    {
        position: 9,
        el: document.getElementById('carousel-item-10'),
    },
    {
        position: 10,
        el: document.getElementById('carousel-item-11'),
    },
];

// options with default values
const options = {
    defaultPosition: 1,
    interval: 3000,

    indicators: {
        activeClasses: 'bg-white dark:bg-gray-800',
        inactiveClasses:
            'bg-white/50 dark:bg-gray-800/50 hover:bg-white dark:hover:bg-gray-800',
        items: [
            {
                position: 0,
                el: document.getElementById('carousel-indicator-1'),
            },
            {
                position: 1,
                el: document.getElementById('carousel-indicator-2'),
            },
            {
                position: 2,
                el: document.getElementById('carousel-indicator-3'),
            },
            {
                position: 3,
                el: document.getElementById('carousel-indicator-4'),
            },
            {
                position: 4,
                el: document.getElementById('carousel-indicator-5'),
            },
            {
                position: 5,
                el: document.getElementById('carousel-indicator-6'),
            },
            {
                position: 6,
                el: document.getElementById('carousel-indicator-7'),
            },
            {
                position: 7,
                el: document.getElementById('carousel-indicator-8'),
            },
            {
                position: 8,
                el: document.getElementById('carousel-indicator-9'),
            },
            {
                position: 9,
                el: document.getElementById('carousel-indicator-10'),
            },
            {
                position: 10,
                el: document.getElementById('carousel-indicator-11'),
            },
        ],
    },

    // callback functions
    onNext: () => {
        console.log('next slider item is shown');
    },
    onPrev: () => {
        console.log('previous slider item is shown');
    },
    onChange: () => {
        console.log('new slider item has been shown');
    },
};

// instance options object
const instanceOptions = {
  id: 'carousel-example',
  override: true
};

const carousel = new Carousel(carouselElement, items, options, instanceOptions);