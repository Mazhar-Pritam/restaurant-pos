import { useState } from 'react';
import { Navigate, useNavigate } from 'react-router-dom';
import { useAuth } from '../AuthContext';

export default function Login() {
  const { user, login } = useAuth();
  const navigate = useNavigate();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [remember, setRemember] = useState(false);
  const [error, setError] = useState('');
  const [submitting, setSubmitting] = useState(false);

  if (user) return <Navigate to="/dashboard" replace />;

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setSubmitting(true);
    try {
      await login(email, password, remember);
      navigate('/dashboard');
    } catch (err) {
      if (err.response?.status === 429) {
        setError('Too many attempts. Please wait a minute and try again.');
      } else {
        setError(err.response?.data?.errors?.email?.[0] ?? 'Login failed. Please try again.');
      }
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="container py-5">
      <div className="row justify-content-center">
        <div className="col-md-5 col-lg-4">
          <div className="card shadow-sm">
            <div className="card-body p-4">
              <h1 className="h4 mb-3">Sign in</h1>
              {error && <div className="alert alert-danger py-2">{error}</div>}
              <form onSubmit={handleSubmit}>
                <div className="mb-3">
                  <label className="form-label" htmlFor="email">Email</label>
                  <input id="email" type="email" className="form-control" required autoFocus
                         value={email} onChange={(e) => setEmail(e.target.value)} />
                </div>
                <div className="mb-3">
                  <label className="form-label" htmlFor="password">Password</label>
                  <input id="password" type="password" className="form-control" required
                         value={password} onChange={(e) => setPassword(e.target.value)} />
                </div>
                <div className="form-check mb-3">
                  <input id="remember" type="checkbox" className="form-check-input"
                         checked={remember} onChange={(e) => setRemember(e.target.checked)} />
                  <label className="form-check-label" htmlFor="remember">Remember me</label>
                </div>
                <button className="btn btn-primary w-100" disabled={submitting}>
                  {submitting ? 'Signing in...' : 'Login'}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
