import { useEffect, useState } from 'react';
import api from '../../api/axios';
import ContractForm from '../user/ContractForm';
import { X, FileText } from 'lucide-react';

export default function ManagerDashboard() {
  const [activeTab, setActiveTab] = useState('pending'); // pending | approved | rejected
  const [requests, setRequests] = useState([]);
  const [loading, setLoading] = useState(true);
  const [modalRequest, setModalRequest] = useState(null); // request selected for contract generation
  const [message, setMessage] = useState('');

  const fetchRequests = async () => {
    setLoading(true);
    try {
      const res = await api.get('/contract-requests', { params: { status: activeTab } });
      setRequests(res.data);
    } catch (err) {
      setMessage('Failed to load requests');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchRequests(); }, [activeTab]);

  const handleDecision = async (id, status) => {
    try {
      await api.patch(`/contract-requests/${id}/status`, { status });
      setMessage(status === 'approved'
        ? 'Request approved! Now generate the contract for this customer.'
        : 'Request rejected.');
      fetchRequests();
    } catch (err) {
      setMessage(err.response?.data?.message || 'Action failed');
    }
  };

  const openContractModal = (request) => {
    setModalRequest(request);
  };

  const handleContractGenerated = () => {
    setModalRequest(null);
    setMessage('Contract generated successfully! The customer can now view and pay for it.');
    fetchRequests();
  };

  return (
    <div className="page">
      <div className="page-header">
        <div>
          <h1>Contract Requests</h1>
          <p className="page-subtitle">Review, approve, and generate contracts for customers</p>
        </div>
      </div>

      {message && <div className="alert-success">{message}</div>}

      <div className="tabs-row">
        <button className={`tab-btn ${activeTab === 'pending' ? 'active' : ''}`} onClick={() => setActiveTab('pending')}>
          Pending
        </button>
        <button className={`tab-btn ${activeTab === 'approved' ? 'active' : ''}`} onClick={() => setActiveTab('approved')}>
          Approved
        </button>
        <button className={`tab-btn ${activeTab === 'rejected' ? 'active' : ''}`} onClick={() => setActiveTab('rejected')}>
          Rejected
        </button>
      </div>

      {loading ? (
        <p style={{ color: 'var(--text-muted)' }}>Loading requests...</p>
      ) : requests.length === 0 ? (
        <div className="empty-state">
          <div className="empty-state-icon">📋</div>
          <p>No {activeTab} requests found.</p>
        </div>
      ) : (
        <div className="table-wrapper">
          <table className="data-table">
            <thead>
              <tr>
                <th>Customer</th><th>Motorcycle</th><th>Requested</th><th>Status</th><th>Action</th>
              </tr>
            </thead>
            <tbody>
              {requests.map((r) => (
                <tr key={r.id}>
                  <td>{r.user.full_name}</td>
                  <td>{r.motorcycle.brand} {r.motorcycle.model}</td>
                  <td>{new Date(r.created_at).toLocaleDateString()}</td>
                  <td><span className={`status-badge status-${r.status}`}>{r.status}</span></td>
                  <td>
                    {activeTab === 'pending' && (
                      <>
                        <button className="btn-small btn-approve" onClick={() => handleDecision(r.id, 'approved')}>Approve</button>
                        <button className="btn-small btn-reject" onClick={() => handleDecision(r.id, 'rejected')}>Reject</button>
                      </>
                    )}

                    {activeTab === 'approved' && (
                      r.contract ? (
                        <span className="contract-generated-tag">
                          <FileText size={13} /> Contract Generated
                        </span>
                      ) : (
                        <button className="btn-small btn-edit" onClick={() => openContractModal(r)}>
                          Generate Contract
                        </button>
                      )
                    )}

                    {activeTab === 'rejected' && <span style={{ color: 'var(--text-muted)', fontSize: 12.5 }}>—</span>}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {modalRequest && (
        <div className="modal-overlay" onClick={() => setModalRequest(null)}>
          <div className="modal-box" onClick={(e) => e.stopPropagation()}>
            <button className="modal-close" onClick={() => setModalRequest(null)}><X size={18} /></button>
            <h2>Generate Contract</h2>
            <p style={{ fontSize: 13, color: 'var(--text-muted)', marginBottom: 16 }}>
              For <strong>{modalRequest.user.full_name}</strong> — {modalRequest.motorcycle.brand} {modalRequest.motorcycle.model}
            </p>
            <ContractForm
              contractRequestId={modalRequest.id}
              motorcyclePrice={modalRequest.motorcycle.total_contract_price}
              onSuccess={handleContractGenerated}
            />
          </div>
        </div>
      )}
    </div>
  );
}