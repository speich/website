let remote = null;

function openWin(url, title, x, y) {
  if (remote && remote.open && !remote.closed) {
    remote.close();
  }
  remote = window.open(url, title, 'width=' + x + ',height=' + y + ',toolbar=no,menubar=no,location=no,scrollbars=no,resizable=yes');
  return false;
}