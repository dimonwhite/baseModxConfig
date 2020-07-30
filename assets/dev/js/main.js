import '@dev/scss/main.scss';
import svg4everybody from 'svg4everybody';

function importAll(r) {
  return r.keys().map(r);
}

importAll(require.context('./../sprite/', false, /\.svg$/));

svg4everybody();
