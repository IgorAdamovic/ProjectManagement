export default process.env.NODE_ENV === 'production'
  ? 'http://sandbox.recognite.co.uk/project_management'
  : 'http://localhost:8888/crud-vue-php/public'
