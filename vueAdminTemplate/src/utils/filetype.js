const imageExts = [
  'bmp',
  'cod',
  'gif',
  'ief',
  'jpe',
  'jpeg',
  'jpg',
  'jfif',
  'svg',
  'tif',
  'tiff',
  'ras',
  'cmx',
  'ico',
  'pnm',
  'pbm',
  'pgm',
  'ppm',
  'rgb',
  'xbm',
  'xpm',
  'xwd',
  'png'
]

const videoExts = [
  'mp2',
  'mpa',
  'mpe',
  'mpeg',
  'mpg',
  'mpv2',
  'mov',
  'qt',
  'lsf',
  'lsx',
  'asf',
  'asr',
  'asx',
  'avi',
  'movie',
  'mp4',
  'flv',
  'ogg',
  'webm'
]

const audioExts = [
  'au',
  'snd',
  'mid',
  'rmi',
  'mp3',
  'aif',
  'aifc',
  'aiff',
  'm3u',
  'ra',
  'ram',
  'wav'
]

export function isImage(fileName) {
  return imageExts.findIndex(ext => _.endsWith(fileName, '.' + ext)) >= 0
}

export function isVideo(fileName) {
  return videoExts.findIndex(ext => _.endsWith(fileName, '.' + ext)) >= 0
}

export function isAudio(fileName) {
  return audioExts.findIndex(ext => _.endsWith(fileName, '.' + ext)) >= 0
}
