import { useState } from 'react';
import api from '../../api/axios';
import LocationPicker from '../../components/LocationPicker';

export default function SellMotorcycle() {
  const [form, setForm] = useState({
    brand: '', model: '', year: '', sale_price: '', condition: 'used', description: '',
    latitude: '', longitude: '', location_name: '',
  });
  const [photos, setPhotos] = useState([]);
  const [message, setMessage] = useState('');
  const [errors, setErrors] = useState({});

  const validate = () => {
    const errs = {};
    if (!form.brand) errs.brand = 'Brand is required';
    if (!form.model) errs.model = 'Model is required';
    if (!form.year || form.year < 1980) errs.year = 'Enter a valid year';
    if (!form.sale_price || form.sale_price <= 0) errs.sale_price = 'Enter a valid price';
    return errs;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const errs = validate();
    setErrors(errs);
    if (Object.keys(errs).length > 0) return;

    const data = new FormData();
    Object.entries(form).forEach(([k, v]) => data.append(k, v));
    photos.forEach((p) => data.append('photos[]', p));

    try {
      await api.post('/motorcycles/sell', data, { headers: { 'Content-Type': 'multipart/form-data' } });
      setMessage('Motorcycle listed for sale successfully!');
      setForm({ brand: '', model: '', year: '', sale_price: '', condition: 'used', description: '', latitude: '', longitude: '', location_name: '' });
      setPhotos([]);
    } catch (err) {
      setMessage(err.response?.data?.message || 'Listing failed');
    }
  };

  return (
    <div className="page">
      <h1>Sell Your Motorcycle</h1>
      {message && <div className="alert-success">{message}</div>}
      <form className="contract-form contract-form-wide" onSubmit={handleSubmit} encType="multipart/form-data">
        <input placeholder="Brand" value={form.brand} onChange={(e) => setForm({ ...form, brand: e.target.value })} />
        {errors.brand && <span className="field-error">{errors.brand}</span>}

        <input placeholder="Model" value={form.model} onChange={(e) => setForm({ ...form, model: e.target.value })} />
        {errors.model && <span className="field-error">{errors.model}</span>}

        <input type="number" placeholder="Year" value={form.year} onChange={(e) => setForm({ ...form, year: e.target.value })} />
        {errors.year && <span className="field-error">{errors.year}</span>}

        <input type="number" placeholder="Selling Price" value={form.sale_price} onChange={(e) => setForm({ ...form, sale_price: e.target.value })} />
        {errors.sale_price && <span className="field-error">{errors.sale_price}</span>}

        <select value={form.condition} onChange={(e) => setForm({ ...form, condition: e.target.value })}>
          <option value="new">New</option>
          <option value="used">Used</option>
        </select>

        <textarea placeholder="Description" value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} />

        <input type="file" multiple accept="image/*" onChange={(e) => setPhotos([...e.target.files])} />

        <input
          placeholder="Location name (e.g. Dox, Dodoma)"
          value={form.location_name}
          onChange={(e) => setForm({ ...form, location_name: e.target.value })}
          style={{ marginTop: 10 }}
        />

        <LocationPicker
          latitude={form.latitude ? Number(form.latitude) : null}
          longitude={form.longitude ? Number(form.longitude) : null}
          onChange={(lat, lng) => setForm({ ...form, latitude: lat, longitude: lng })}
        />

        <button type="submit" className="btn-primary">List Motorcycle</button>
      </form>
    </div>
  );
}