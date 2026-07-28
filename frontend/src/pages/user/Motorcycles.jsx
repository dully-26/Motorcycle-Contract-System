import { useEffect, useState } from 'react';
import api from '../../api/axios';

export default function Motorcycles() {
  const [motorcycles, setMotorcycles] = useState([]);
  const [search, setSearch] = useState('');
  const [loading, setLoading] = useState(true);
  const [message, setMessage] = useState('');

  const fetchMotorcycles = async () => {
    setLoading(true);
    const res = await api.get('/motorcycles', { params: { listing_type: 'contract', search } });
    setMotorcycles(res.data.data);
    setLoading(false);
  };

  useEffect(() => { fetchMotorcycles(); }, [search]);

  const requestContract = async (motorcycleId) => {
    try {
      await api.post('/contract-requests', { motorcycle_id: motorcycleId });
      setMessage('Contract request submitted successfully!');
      fetchMotorcycles();
    } catch (err) {
      setMessage(err.response?.data?.message || 'Request failed');
    }
  };

  return (
    <div className="page">
      <div className="page-header">
        <h1>Available Motorcycles</h1>
        <input
          className="search-input"
          placeholder="Search brand or model..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
        />
      </div>

      {message && <div className="alert-success">{message}</div>}
      {loading ? <p>Loading...</p> : (
        <div className="card-grid">
          {motorcycles.map((m) => (
            <div className="motorcycle-card" key={m.id}>
              <div className="card-image">
                {m.photos?.[0] ? (
                  <img src={`http://localhost:8000/storage/${m.photos[0]}`} alt={m.model} />
                ) : (
                  <div className="no-image">No Image</div>
                )}
                <span className={`status-badge status-${m.status}`}>{m.status}</span>
              </div>
              <div className="card-body">
                <h3>{m.brand} {m.model}</h3>
                <p className="year">{m.year} • {m.condition}</p>
                <div className="price-row">
                  <span>Daily: TZS {m.daily_price}</span>
                  <span>Monthly: TZS {m.monthly_price}</span>
                </div>
                <p className="total-price">Total: TZS {m.total_contract_price}</p>
                <button
                  className="btn-primary"
                  disabled={m.status !== 'available'}
                  onClick={() => requestContract(m.id)}
                >
                  {m.status === 'available' ? 'Request Contract' : 'Not Available'}
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}