public boolean login(String username, String password) {
    String query = "SELECT password_hash FROM users WHERE username = ?";
    
    try (Connection conn = db.getConnection();
         PreparedStatement pstmt = conn.prepareStatement(query)) {
        
        pstmt.setString(1, username);
        ResultSet rs = pstmt.executeQuery();
        
        if (rs.next()) {
            String storedHash = rs.getString("password_hash");
            return BCrypt.checkpw(password, storedHash);
        }
    } catch (SQLException e) {
        e.printStackTrace();
    }
    return false;
}
