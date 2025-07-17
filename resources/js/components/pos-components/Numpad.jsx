/* eslint-disable-next-line */
export const Numpad = ({setSearchValue,clearSearch,searchValue,validateSearch,}) => {
  const handleClick = (number) => {
    let search = searchValue + number;
    setSearchValue(search);
  };
  return (
    <>
      <div className="col-12">
        <div className="card">
          <div className="card-body">
            <div className="row">
              <div className="col-4 p-2">
                <button
                  className="btn btn-light w-100 py-3 "
                  onClick={() => handleClick(7)}
                >
                  <h3 className="m-0 text-primary my-3">7</h3>
                </button>
              </div>
              <div className="col-4 p-2">
                <button
                  className="btn btn-light w-100 py-3 "
                  onClick={() => handleClick(8)}
                >
                  <h3 className="m-0 text-primary my-3">8</h3>
                </button>
              </div>
              <div className="col-4 p-2">
                <button
                  className="btn btn-light w-100 py-3 "
                  onClick={() => handleClick(9)}
                >
                  <h3 className="m-0 text-primary my-3">9</h3>
                </button>
              </div>
              <div className="col-4 p-2">
                <button
                  className="btn btn-light w-100 py-3 "
                  onClick={() => handleClick(4)}
                >
                  <h3 className="m-0 text-primary my-3">4</h3>
                </button>
              </div>
              <div className="col-4 p-2">
                <button
                  className="btn btn-light w-100 py-3 "
                  onClick={() => handleClick(5)}
                >
                  <h3 className="m-0 text-primary my-3">5</h3>
                </button>
              </div>
              <div className="col-4 p-2">
                <button
                  className="btn btn-light w-100 py-3 "
                  onClick={() => handleClick(6)}
                >
                  <h3 className="m-0 text-primary my-3">6</h3>
                </button>
              </div>
              <div className="col-4 p-2">
                <button
                  className="btn btn-light w-100 py-3 "
                  onClick={() => handleClick(1)}
                >
                  <h3 className="m-0 text-primary my-3">1</h3>
                </button>
              </div>
              <div className="col-4 p-2">
                <button
                  className="btn btn-light w-100 py-3 "
                  onClick={() => handleClick(2)}
                >
                  <h3 className="m-0 text-primary my-3">2</h3>
                </button>
              </div>
              <div className="col-4 p-2">
                <button
                  className="btn btn-light w-100 py-3 "
                  onClick={() => handleClick(3)}
                >
                  <h3 className="m-0 text-primary my-3">3</h3>
                </button>
              </div>
              <div className="col-4 p-2">
                <button
                  className="btn btn-danger w-100 py-3 "
                  onClick={() => clearSearch()}
                >
                  <h3 className="m-0 my-3">
                    <div className="fas fa-times"></div>
                  </h3>
                </button>
              </div>
              <div className="col-4 p-2">
                <button
                  className="btn btn-light w-100 py-3 "
                  onClick={() => handleClick(0)}
                >
                  <h3 className="m-0 text-primary my-3">0</h3>
                </button>
              </div>
              <div className="col-4 p-2">
                <button
                  className="btn btn-success w-100 py-3"
                  onClick={(event) => validateSearch(event)}
                >
                  <h3 className="m-0 my-3" style={{pointerEvents:'none'}}>
                    <i className="fa fa-check" style={{pointerEvents:'none'}}></i>
                  </h3>
                </button>
              </div>
              <div className="col-4 p-2">
                <button
                  className="btn btn-light w-100 py-3 "
                  onClick={() => handleClick('P')}
                >
                  <h3 className="m-0 text-primary my-3">P</h3>
                </button>
              </div>
              <div className="col-4 p-2">
                <button
                  className="btn btn-light w-100 py-3 "
                  onClick={() => handleClick('SP')}
                >
                  <h3 className="m-0 text-primary my-3">SP</h3>
                </button>
              </div>
              <div className="col-4 p-2">
                <button
                  className="btn btn-light w-100 py-3 "
                  onClick={() => handleClick('G')}
                >
                  <h3 className="m-0 text-primary my-3">G</h3>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};
