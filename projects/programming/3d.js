/********************************************
 *  Created by Simon Speich, www.speich.net *
 *  lastChange v1.1 05.11.2021              *
 *  v1.0.1 24.10.2009                       *
 *  v1.0, 02.12.2003                        *
 /*******************************************/

/**
 *
 * @type {{mCube: number[][], displArea: {}, origin: {v: number[]}, function: [], playing: boolean, cube: *[], drawLine: app.drawLine, mI: number[][], cam: *[], mTrans: number[][], removeLinePx: app.removeLinePx}}
 */

/**
 * Create a pixel.
 * @param x x-coordinate
 * @param y y-coordinate
 * @param z z-coordinate
 */
let Pixel = class {
  constructor(x, y, z) {
    let el = document.createElement('div');

    el.style.top = y + 'px';
    el.style.left = x + 'px';
    el.classList.add('pixel');

    document.getElementById('viewArea').appendChild(el);
    this.v = [x, y, z, 1];
  }
};

let app = {
  playing: false,
  cube: [],
  origin: {
    v: []
  },
  canvas: {
    width: 500,
    height: 500
  },
  cubeSize: 80,
  maxPx: null,
  // camera vector
  cam: [],
  // transformation matrix
  mTrans: [
    [1, 0, 0, 0],
    [0, 1, 0, 0],
    [0, 0, 1, 0],
    [0, 0, 0, 1]
  ],
  // position information of cube
  mCube: [
    [1, 0, 0, 0],
    [0, 1, 0, 0],
    [0, 0, 1, 0],
    [0, 0, 0, 1]
  ],
  mI: [
    [1, 0, 0, 0],
    [0, 1, 0, 0],
    [0, 0, 1, 0],
    [0, 0, 0, 1]
  ],

  /**
   *
   * @param {origin} from
   * @param {origin} to
   */
  drawLine(from, to) {
    let x1 = from.v[0],
      x2 = to.v[0],
      y1 = from.v[1],
      y2 = to.v[1],
      dx = Math.abs(x2 - x1),
      dy = Math.abs(y2 - y1),
      x = x1,
      y = y1,
      incX1, incY1,
      incX2, incY2,
      den,
      num,
      numAdd,
      numPix;

    if (x2 >= x1) {
      incX1 = 1;
      incX2 = 1;
    } else {
      incX1 = -1;
      incX2 = -1;
    }
    if (y2 >= y1) {
      incY1 = 1;
      incY2 = 1;
    } else {
      incY1 = -1;
      incY2 = -1;
    }
    if (dx >= dy) {
      incX1 = 0;
      incY2 = 0;
      den = dx;
      num = dx / 2;
      numAdd = dy;
      numPix = dx;
    } else {
      incX2 = 0;
      incY1 = 0;
      den = dy;
      num = dy / 2;
      numAdd = dx;
      numPix = dy;
    }

    numPix = Math.round(this.cube.lastPx + numPix);
    let pixels = this.getPixels();
    let i = this.cube.lastPx;
    for (; i < numPix; i++) {
      pixels[i].style.top = y + 'px';
      pixels[i].style.left = x + 'px';
      num += numAdd;
      if (num >= den) {
        num -= den;
        x += incX1;
        y += incY1;
      }
      x += incX2;
      y += incY2;
    }
    this.cube.lastPx = numPix;
  },

  removeLinePx() {
    // not working correctly yet
    let last = this.cube.lastPx,
      pixels = this.getPixels(),
      i = pixels.length - 1;
    for (; i >= last; i--) {
      pixels[i].style.visibility = 'hidden';
    }
  },

  /**
   * Returns all pixels.
   * @return {NodeListOf<Element>}
   */
  getPixels() {
    return document.querySelectorAll('#viewArea > div');
  },

  /**
   *
   * @param {array} V0 vector
   * @param {array} V1 vector
   * @return {*[]}
   */
  calcCross(V0, V1) {
    let cross = [];

    cross[0] = V0[1] * V1[2] - V0[2] * V1[1];
    cross[1] = V0[2] * V1[0] - V0[0] * V1[2];
    cross[2] = V0[0] * V1[1] - V0[1] * V1[0];

    return cross;
  },

  /**
   *
   * @param {array} V0 vector
   * @param {array} V1 vector
   * @param {array} V2 vector
   * @return {*[]}
   */
  calcNormal(V0, V1, V2) {
    let A = [];
    let B = [];

    for (let i = 0; i < 3; i++) {
      A[i] = V0[i] - V1[i];
      B[i] = V2[i] - V1[i];
    }
    A = this.calcCross(A, B);
    let length = Math.sqrt(A[0] * A[0] + A[1] * A[1] + A[2] * A[2]);
    for (let i = 0; i < 3; i++) {
      A[i] /= length;
    }
    A[3] = 1;

    return A;
  },

  /**
   * Multiply two matrices
   * @param {array} M1 matrix
   * @param {array} M2 matrix
   * @return {array}
   */
  matrixMultiply(M1, M2) {
    let M = [[], [], [], []],
      i = 0;

    for (; i < 4; i++) {
      let j = 0;
      for (; j < 4; j++) {
        M[i][j] = M1[i][0] * M2[0][j] + M1[i][1] * M2[1][j] + M1[i][2] * M2[2][j] + M1[i][3] * M2[3][j];
      }
    }

    return M;
  },

  /**
   * Multiply a matrix with a vector
   * @param {array} M matrix
   * @param {array} v vector
   * @return {any[]}
   */
  vectorMultiply(M, v) {
    let newV = [],
      i = 0;

    for (; i < 4; i++) {
      newV[i] = M[i][0] * v[0] + M[i][1] * v[1] + M[i][2] * v[2] + M[i][3] * v[3];
    }

    return newV;
  },

  /**
   *
   * @param M
   * @param v
   * @return {*[]}
   */
  vectorMultiply2(M, v) {
    let newV = [],
      i = 0;
    for (; i < 3; i++) {
      newV[i] = M[i][0] * v[0] + M[i][1] * v[1] + M[i][2] * v[2];
    }

    return newV;
  },

  /**
   *
   * @param M
   * @param dx
   * @param dy
   * @param dz
   * @return {Array}
   */
  translate(M, dx, dy, dz) {
    let T = [
      [1, 0, 0, dx],
      [0, 1, 0, dy],
      [0, 0, 1, dz],
      [0, 0, 0, 1]
    ];

    return this.matrixMultiply(T, M);
  },

  rotateX(M, phi) {
    let cos, sin, R, a = phi;

    a *= Math.PI / 180;
    cos = Math.cos(a);
    sin = Math.sin(a);
    R = [
      [1, 0, 0, 0],
      [0, cos, -sin, 0],
      [0, sin, cos, 0],
      [0, 0, 0, 1]
    ];

    return this.matrixMultiply(R, M);
  },

  rotateY(M, phi) {
    let cos, sin, R, a = phi;

    a *= Math.PI / 180;
    cos = Math.cos(a);
    sin = Math.sin(a);
    R = [
      [cos, 0, sin, 0],
      [0, 1, 0, 0],
      [-sin, 0, cos, 0],
      [0, 0, 0, 1]
    ];

    return this.matrixMultiply(R, M);
  },

  rotateZ(M, phi) {
    let cos, sin, R, a = phi;

    a *= Math.PI / 180;
    cos = Math.cos(a);
    sin = Math.sin(a);
    R = [
      [cos, -sin, 0, 0],
      [sin, cos, 0, 0],
      [0, 0, 1, 0],
      [0, 0, 0, 1]
    ];

    return this.matrixMultiply(R, M);
  },

  drawCube() {
    let cube = this.cube,
      currNormal = [],
      i = 5;

    cube.lastPx = 0;
    for (; i > -1; i--) {
      currNormal[i] = this.vectorMultiply2(this.mCube, this.cube.normal[i]);
    }

    if (currNormal[0][2] < 0) {
      if (!cube.line[0]) {
        this.drawLine(cube[0], cube[1]);
        cube.line[0] = true;
      }
      if (!cube.line[1]) {
        this.drawLine(cube[1], cube[2]);
        cube.line[1] = true;
      }
      if (!cube.line[2]) {
        this.drawLine(cube[2], cube[3]);
        cube.line[2] = true;
      }
      if (!cube.line[3]) {
        this.drawLine(cube[3], cube[0]);
        cube.line[3] = true;
      }
    }
    if (currNormal[1][2] < 0) {
      if (!cube.line[2]) {
        this.drawLine(cube[3], cube[2]);
        cube.line[2] = true;
      }
      if (!cube.line[9]) {
        this.drawLine(cube[2], cube[6]);
        cube.line[9] = true;
      }
      if (!cube.line[6]) {
        this.drawLine(cube[6], cube[7]);
        cube.line[6] = true;
      }
      if (!cube.line[10]) {
        this.drawLine(cube[7], cube[3]);
        cube.line[10] = true;
      }
    }
    if (currNormal[2][2] < 0) {
      if (!cube.line[4]) {
        this.drawLine(cube[4], cube[5]);
        cube.line[4] = true;
      }
      if (!cube.line[5]) {
        this.drawLine(cube[5], cube[6]);
        cube.line[5] = true;
      }
      if (!cube.line[6]) {
        this.drawLine(cube[6], cube[7]);
        cube.line[6] = true;
      }
      if (!cube.line[7]) {
        this.drawLine(cube[7], cube[4]);
        cube.line[7] = true;
      }
    }
    if (currNormal[3][2] < 0) {
      if (!cube.line[4]) {
        this.drawLine(cube[4], cube[5]);
        cube.line[4] = true;
      }
      if (!cube.line[8]) {
        this.drawLine(cube[5], cube[1]);
        cube.line[8] = true;
      }
      if (!cube.line[0]) {
        this.drawLine(cube[1], cube[0]);
        cube.line[0] = true;
      }
      if (!cube.line[11]) {
        this.drawLine(cube[0], cube[4]);
        cube.line[11] = true;
      }
    }
    if (currNormal[4][2] < 0) {
      if (!cube.line[11]) {
        this.drawLine(cube[4], cube[0]);
        cube.line[11] = true;
      }
      if (!cube.line[3]) {
        this.drawLine(cube[0], cube[3]);
        cube.line[3] = true;
      }
      if (!cube.line[10]) {
        this.drawLine(cube[3], cube[7]);
        cube.line[10] = true;
      }
      if (!cube.line[7]) {
        this.drawLine(cube[7], cube[4]);
        cube.line[7] = true;
      }
    }
    if (currNormal[5][2] < 0) {
      if (!cube.line[8]) {
        this.drawLine(cube[1], cube[5]);
        cube.line[8] = true;
      }
      if (!cube.line[5]) {
        this.drawLine(cube[5], cube[6]);
        cube.line[5] = true;
      }
      if (!cube.line[9]) {
        this.drawLine(cube[6], cube[2]);
        cube.line[9] = true;
      }
      if (!cube.line[1]) {
        this.drawLine(cube[2], cube[1]);
        cube.line[1] = true;
      }
    }
    cube.line = [false, false, false, false, false, false, false, false, false, false, false, false];
    cube.lastPx = 0;
  },

  /**
   *
   * @param {Event} evt
   */
  getMousePos(evt) {
    let w = this.canvas.width / 2,
      h = this.canvas.height / 2,
      el = document.getElementById('viewArea'),
      left = this.findPos(el)[0],
      top = this.findPos(el)[1],
      x = evt.clientX - left,
      y = evt.clientY - top;

    if (x > 0 && x < this.canvas.width) {
      x -= w;
    } else {
      x = 0;
    }
    if (y > 0 && y < this.canvas.height) {
      y -= h;
    } else {
      y = 0;
    }
    this.cam[1] = y / 12;
    this.cam[0] = -x / 12;
  },

  loop() {
    let pixels, cam, cube, mTrans;

    if (this.playing) {
      pixels = this.getPixels();
      cam = this.cam;
      cube = this.cube;
      mTrans = this.mTrans;
      mTrans = this.translate(this.mI, -cube[8].v[0], -cube[8].v[1], -cube[8].v[2]);
      mTrans = this.rotateX(mTrans, cam[1]);
      mTrans = this.rotateY(mTrans, cam[0]);
      cam[2] = (cube[8].v[0] - this.canvas.width / 2 - cam[0] * 12 * -1) / 10 * -1;
      cam[3] = (cube[8].v[1] - this.canvas.height / 2 - cam[1] * 12) / 10 * -1;
      mTrans = this.translate(mTrans, cam[2], cam[3], 0);
      mTrans = this.translate(mTrans, cube[8].v[0], cube[8].v[1], cube[8].v[2]);
      this.mCube = this.matrixMultiply(mTrans, this.mCube);
      let i = 8;
      for (; i > -1; i--) {
        cube[i].v = this.vectorMultiply(mTrans, cube[i].v);
        pixels[i].style.left = Math.round(cube[i].v[0]) + 'px';
        pixels[i].style.top = Math.round(cube[i].v[1]) + 'px';
      }
      this.drawCube();
    }

    window.requestAnimationFrame(this.loop.bind(this));
  },

  start() {
    let cube = this.cube;

    cube[0] = new Pixel(-this.cubeSize / 2, -this.cubeSize / 2, this.cubeSize / 2);
    cube[1] = new Pixel(-this.cubeSize / 2, this.cubeSize / 2, this.cubeSize / 2);
    cube[2] = new Pixel(this.cubeSize / 2, this.cubeSize / 2, this.cubeSize / 2);
    cube[3] = new Pixel(this.cubeSize / 2, -this.cubeSize / 2, this.cubeSize / 2);
    cube[4] = new Pixel(-this.cubeSize / 2, -this.cubeSize / 2, -this.cubeSize / 2);
    cube[5] = new Pixel(-this.cubeSize / 2, this.cubeSize / 2, -this.cubeSize / 2);
    cube[6] = new Pixel(this.cubeSize / 2, this.cubeSize / 2, -this.cubeSize / 2);
    cube[7] = new Pixel(this.cubeSize / 2, -this.cubeSize / 2, -this.cubeSize / 2);
    // center of gravity
    cube[8] = new Pixel(0, 0, 0);
    // anti-clockwise edge check
    cube.edge = [[0, 1, 2], [3, 2, 6], [7, 6, 5], [4, 5, 1], [4, 0, 3], [1, 5, 6]];
    // calculate squad normals
    cube.normal = [];
    for (let i = 0; i < cube.edge.length; i++) {
      cube.normal[i] = this.calcNormal(cube[cube.edge[i][0]].v, cube[cube.edge[i][1]].v, cube[cube.edge[i][2]].v);
    }
    // line drawn ?
    cube.line = [false, false, false, false, false, false, false, false, false, false, false, false];
    // create line pixels
    for (let i = 0; i < this.maxPx; i++) {
      new Pixel(0, 0, 0);
    }

    this.mTrans = this.translate(this.mTrans, this.origin.v[0], this.origin.v[1], this.origin.v[2]);
    this.mCube = this.matrixMultiply(this.mTrans, this.mCube);

    let pixels = this.getPixels();
    let i = 0;
    for (; i < 9; i++) {
      cube[i].v = this.vectorMultiply(this.mTrans, cube[i].v);
      pixels[i].style.left = Math.round(cube[i].v[0]) + 'px';
      pixels[i].style.top = Math.round(cube[i].v[1]) + 'px';
    }
    this.drawCube();
    this.loop();
  },

  findPos(obj) {
    // code by http://www.quirksmode.org/js/findpos.html
    let left = 0, top = 0;

    if (obj.offsetParent) {
      do {
        left += obj.offsetLeft;
        top += obj.offsetTop;
      }
      while (obj = obj.offsetParent);

      return [left, top];
    }
  },

  init() {
    this.area = document.getElementById('viewAreaCont');
    this.area.style.width = this.canvas.width + 'px';
    this.area.style.height = this.canvas.height + 'px';
    this.maxPx = this.cubeSize * this.cubeSize;
    this.origin.v = [this.canvas.width / 2, this.canvas.height / 2, this.cubeSize / 2, 1];
    this.initEvents();
    this.start();
  },

  initEvents() {
    document.getElementById('opener').addEventListener('click', (evt) => {
      evt.preventDefault();
      openWin('3d.htm', 'www.speich.net - Mozilla Testcase for Bug 229391', 330, 560);
    });
    document.addEventListener('mousemove', this.getMousePos.bind(this), true);
    document.getElementById('viewArea').addEventListener('click', () => {
      app.playing = !app.playing;
    });
  }
};

function openWin(url, title, x, y) {
  let remote = openWin.remote || null;

  if (remote && remote.open && !remote.closed) {
    remote.close();
  }

  openWin.remote = window.open(url, title, 'width=' + x + ',height=' + y + ',toolbar=no,menubar=no,location=no,scrollbars=no,resizable=yes');
}

window.addEventListener('load', app.init.bind(app));