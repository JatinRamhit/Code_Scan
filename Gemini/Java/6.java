@WebServlet("/redirect")
public class RedirectServlet extends HttpServlet {
    private final Set<String> ALLOWED_HOSTS = Set.of("dashboard.mysite.com", "profile.mysite.com");

    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws IOException {
        String target = request.getParameter("dest");

        if (target != null && ALLOWED_HOSTS.contains(target)) {
            response.sendRedirect("https://" + target);
        } else {
            response.sendRedirect("https://mysite.com/home");
        }
    }
}
