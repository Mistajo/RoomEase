/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss'

// A js part of bootstrap
require('bootstrap');

import { startStimulusApp } from '@symfony/stimulus-bridge';
 
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
));
