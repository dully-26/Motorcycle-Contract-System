export async function downloadPdf(url, filename) {
  const token = localStorage.getItem('token');
  const res = await fetch(`http://localhost:8000/api${url}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
  const blob = await res.blob();
  const link = document.createElement('a');
  link.href = window.URL.createObjectURL(blob);
  link.download = filename;
  link.click();
}