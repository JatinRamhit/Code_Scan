public class ProcessorFactory {
    private static final Map<String, Supplier<Processor>> REGISTRY = Map.of(
        "CSV", CsvProcessor::new,
        "XML", XmlProcessor::new
    );

    public Processor getProcessor(String type) {
        Supplier<Processor> supplier = REGISTRY.get(type.toUpperCase());
        if (supplier == null) {
            throw new IllegalArgumentException("Unknown processor type");
        }
        return supplier.get();
    }
}
