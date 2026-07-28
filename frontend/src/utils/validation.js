export const validateEmail = (email) =>
  /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

export const validatePhone = (phone) =>
  /^[0-9+\s-]{7,15}$/.test(phone);

export const validateNida = (nida) =>
  /^\d{8}-\d{5}-\d{5}-\d{2}$/.test(nida) || /^\d{20}$/.test(nida.replace(/-/g, ''));

export const validateRegisterForm = ({ full_name, email, password, password_confirmation, phone }) => {
  const errors = {};
  if (!full_name || full_name.trim().length < 3) errors.full_name = 'Full name is required (min 3 chars)';
  if (!validateEmail(email)) errors.email = 'Enter a valid email address';
  if (!password || password.length < 6) errors.password = 'Password must be at least 6 characters';
  if (password !== password_confirmation) errors.password_confirmation = 'Passwords do not match';
  if (phone && !validatePhone(phone)) errors.phone = 'Enter a valid phone number';
  return errors;
};

export const validateContractForm = ({ witness, guarantor }) => {
  const errors = {};
  if (!witness.full_name) errors.witness_name = 'Witness name required';
  if (!validateNida(witness.nida_number)) errors.witness_nida = 'Invalid NIDA number';
  if (!validatePhone(witness.phone)) errors.witness_phone = 'Invalid phone number';
  if (!guarantor.full_name) errors.guarantor_name = 'Guarantor name required';
  if (!validateNida(guarantor.nida_number)) errors.guarantor_nida = 'Invalid NIDA number';
  if (!validatePhone(guarantor.phone)) errors.guarantor_phone = 'Invalid phone number';
  return errors;
};